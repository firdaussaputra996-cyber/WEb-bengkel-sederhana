<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ServiceNotification;
use App\Models\Service;
use App\Models\SparePart;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with(['user', 'service'])
            ->when($request->search, function ($query, $search) {
                $query->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('service', fn ($service) => $service->where('name', 'like', "%{$search}%"));
            })
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'statuses' => Booking::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('admin.bookings.create', [
            'users' => User::where('role', 'user')->orderBy('name')->get(),
            'services' => Service::orderBy('name')->get(),
            'statuses' => Booking::STATUSES,
            'bookingTypes' => Booking::BOOKING_TYPES,
            'paymentMethods' => Booking::PAYMENT_METHODS,
            'paymentStatuses' => Booking::PAYMENT_STATUSES,
            'addonGroups' => SparePart::groupedOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['booking_code'] = 'MC-' . now()->format('ymd') . '-' . str_pad((string) (Booking::count() + 1), 4, '0', STR_PAD_LEFT);

        Booking::create($data);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking berhasil ditambahkan.');
    }

    public function edit(Booking $booking): View
    {
        return view('admin.bookings.edit', [
            'booking' => $booking,
            'users' => User::where('role', 'user')->orderBy('name')->get(),
            'services' => Service::orderBy('name')->get(),
            'statuses' => Booking::STATUSES,
            'bookingTypes' => Booking::BOOKING_TYPES,
            'paymentMethods' => Booking::PAYMENT_METHODS,
            'paymentStatuses' => Booking::PAYMENT_STATUSES,
            'addonGroups' => SparePart::groupedOptions(),
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $oldStatus = $booking->status;

        $booking->update($this->validated($request));
        $this->notifyIfServiceDone($booking->fresh(['user', 'service']), $oldStatus);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking berhasil diperbarui.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return back()->with('success', 'Booking berhasil dihapus.');
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Booking::STATUSES)],
        ]);

        $oldStatus = $booking->status;

        $booking->update($data);
        $this->notifyIfServiceDone($booking->fresh(['user', 'service']), $oldStatus);

        return back()->with('success', 'Status booking diperbarui.');
    }

    public function verifyPayment(Booking $booking): RedirectResponse
    {
        $booking->update(['payment_status' => 'Lunas']);

        return back()->with('success', 'Pembayaran booking berhasil diverifikasi lunas.');
    }

    public function export(Request $request, string $period): StreamedResponse
    {
        abort_unless(in_array($period, ['today', 'month', 'all'], true), Response::HTTP_NOT_FOUND);

        $query = Booking::with(['user.profile', 'service'])->orderBy('service_date')->orderBy('service_time');

        if ($period === 'today') {
            $query->whereDate('service_date', today());
            $filename = 'laporan-service-hari-ini-' . today()->format('Y-m-d') . '.csv';
            $emptyMessage = 'Tidak ada data service untuk tanggal ' . today()->format('Y-m-d');
        } elseif ($period === 'month') {
            $query->whereYear('service_date', today()->year)
                ->whereMonth('service_date', today()->month);
            $filename = 'laporan-service-bulan-ini-' . today()->format('Y-m') . '.csv';
            $emptyMessage = 'Tidak ada data service untuk bulan ' . today()->format('Y-m');
        } else {
            $filename = 'laporan-service-semua-data-' . today()->format('Y-m-d') . '.csv';
            $emptyMessage = 'Tidak ada data booking service.';
        }

        $hasRows = (clone $query)->exists();

        return response()->streamDownload(function () use ($query, $hasRows, $emptyMessage) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Kode Booking',
                'Tanggal Service',
                'Jam',
                'Pelanggan',
                'Email',
                'No HP',
                'Layanan',
                'Jenis Booking',
                'Motor',
                'Plat Nomor',
                'Keluhan',
                'Add-on',
                'Status',
                'Status Pembayaran',
                'Metode Pembayaran',
                'Referensi Pembayaran',
                'Bukti Pembayaran',
                'Harga Layanan',
                'Total Add-on',
                'Estimasi Total',
                'Catatan Admin',
            ]);

            if (! $hasRows) {
                fputcsv($handle, [
                    $emptyMessage,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ]);

                fclose($handle);
                return;
            }

            $query->chunk(100, function ($bookings) use ($handle) {
                foreach ($bookings as $booking) {
                    fputcsv($handle, [
                        $booking->booking_code,
                        $booking->service_date->format('Y-m-d'),
                        $booking->service_time,
                        $booking->user->name,
                        $booking->user->email,
                        $booking->user->profile->phone ?? '-',
                        $booking->service->name,
                        $booking->booking_type,
                        $booking->motor_name,
                        $booking->plate_number,
                        $booking->complaint,
                        collect($booking->selected_addons ?? [])->map(fn ($addon) => ($addon['name'] ?? '-') . ' x' . ($addon['quantity'] ?? 1))->implode(', '),
                        $booking->status,
                        $booking->payment_status,
                        $booking->payment_method,
                        $booking->payment_reference,
                        $booking->payment_proof ? asset('storage/' . $booking->payment_proof) : '',
                        $booking->service->price,
                        $booking->addon_total,
                        $booking->estimated_total,
                        $booking->admin_note,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'service_id' => ['required', 'exists:services,id'],
            'booking_type' => ['required', Rule::in(Booking::BOOKING_TYPES)],
            'service_date' => ['required', 'date'],
            'service_time' => ['nullable', 'date_format:H:i'],
            'motor_name' => ['required', 'string', 'max:120'],
            'plate_number' => ['nullable', 'string', 'max:30'],
            'complaint' => ['required', 'string', 'min:10', 'max:1000'],
            'selected_addons' => ['nullable', 'array'],
            'selected_addons.*' => ['string'],
            'addon_quantities' => ['nullable', 'array'],
            'addon_quantities.*' => ['integer', 'min:1', 'max:99'],
            'payment_method' => ['nullable', Rule::in(Booking::PAYMENT_METHODS)],
            'payment_status' => ['required', Rule::in(Booking::PAYMENT_STATUSES)],
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'payment_proof' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(Booking::STATUSES)],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        return array_merge($data, $this->totalsFor($request));
    }

    private function totalsFor(Request $request): array
    {
        $service = Service::find($request->integer('service_id'));
        $selectedAddons = SparePart::selectedAddons($request->input('selected_addons', []), $request->input('addon_quantities', []));
        $addonTotal = collect($selectedAddons)->sum('line_total');

        return [
            'selected_addons' => $selectedAddons,
            'addon_total' => $addonTotal,
            'estimated_total' => ($service?->price ?? 0) + $addonTotal,
        ];
    }

    private function notifyIfServiceDone(Booking $booking, string $oldStatus): void
    {
        if ($oldStatus === 'Selesai' || $booking->status !== 'Selesai') {
            return;
        }

        ServiceNotification::firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => $booking->user_id,
                'title' => 'Motor selesai diservice',
                'message' => "Motor {$booking->motor_name} untuk booking {$booking->booking_code} telah selesai diservice. Silakan datang ke bengkel untuk pengambilan.",
            ]
        );
    }
}
