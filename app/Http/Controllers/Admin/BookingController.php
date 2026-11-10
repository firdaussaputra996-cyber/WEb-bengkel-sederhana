<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ServiceNotification;
use App\Models\Service;
use App\Models\SparePart;
use App\Models\SparePartUsage;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with(['user', 'service', 'mechanic', 'sparePartUsages.sparePart'])
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
            'mechanics' => User::where('role', 'mechanic')->orderBy('name')->get(),
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
            'mechanics' => User::where('role', 'mechanic')->orderBy('name')->get(),
            'spareParts' => SparePart::where('is_active', true)->orderBy('category')->orderBy('name')->get(),
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
            'mechanics' => User::where('role', 'mechanic')->orderBy('name')->get(),
            'spareParts' => SparePart::where('is_active', true)->orderBy('category')->orderBy('name')->get(),
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
        DB::transaction(function () use ($booking): void {
            $booking->load('sparePartUsages');
            foreach ($booking->sparePartUsages as $usage) {
                SparePart::whereKey($usage->spare_part_id)->increment('stock', $usage->quantity);
            }
            $booking->delete();
        });

        return back()->with('success', 'Booking berhasil dihapus.');
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Booking::STATUSES)],
            'mechanic_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'mechanic')],
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

    public function recordSparePartUsage(Request $request, Booking $booking): RedirectResponse
    {
        $mechanicId = null;
        if ($request->user()?->role === 'mechanic') {
            abort_unless((int) $booking->mechanic_id === (int) $request->user()->id, 403);
            $mechanicId = $request->user()->id;
        }

        $data = $request->validate([
            'spare_part_id' => ['required', 'exists:spare_parts,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        DB::transaction(function () use ($booking, $data, $mechanicId): void {
            $lockedBooking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            abort_unless(! in_array($lockedBooking->status, ['Selesai', 'Dibatalkan'], true), 403);
            if ($mechanicId !== null) {
                abort_unless((int) $lockedBooking->mechanic_id === (int) $mechanicId, 403);
            }

            $part = SparePart::whereKey($data['spare_part_id'])->lockForUpdate()->firstOrFail();
            $usage = SparePartUsage::where('booking_id', $lockedBooking->id)
                ->where('spare_part_id', $part->id)
                ->lockForUpdate()
                ->first();
            $oldQuantity = (int) ($usage?->quantity ?? 0);
            $quantity = (int) $data['quantity'];
            $stockChange = $quantity - $oldQuantity;

            if ($stockChange > 0 && $part->stock < $stockChange) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi. Stok tersedia: '.$part->stock.'.',
                ]);
            }

            $unitPrice = (float) ($usage?->unit_price ?? $part->price);
            if ($stockChange > 0) {
                $part->decrement('stock', $stockChange);
            } elseif ($stockChange < 0) {
                $part->increment('stock', abs($stockChange));
            }

            $quotedAddon = collect($lockedBooking->selected_addons ?? [])->first(function (array $addon) use ($part): bool {
                return \Illuminate\Support\Str::slug(($addon['category'] ?? '').' '.($addon['name'] ?? '')) === $part->option_key;
            });
            $quoteTotal = (float) ($quotedAddon['line_total'] ?? 0);
            $costAdjustment = $usage
                ? ($quantity - $oldQuantity) * $unitPrice
                : ($quantity * $unitPrice) - $quoteTotal;

            SparePartUsage::updateOrCreate(
                ['booking_id' => $lockedBooking->id, 'spare_part_id' => $part->id],
                ['quantity' => $quantity, 'unit_price' => $unitPrice, 'installed_at' => now()],
            );

            $addonTotal = max(0, (float) $lockedBooking->addon_total + $costAdjustment);
            $lockedBooking->update([
                'addon_total' => $addonTotal,
                'estimated_total' => (float) $lockedBooking->service()->value('price') + $addonTotal,
            ]);

            ServiceNotification::create([
                'user_id' => $lockedBooking->user_id,
                'booking_id' => $lockedBooking->id,
                'title' => 'Suku cadang servis diperbarui',
                'message' => "{$quantity} x {$part->name} dicatat terpasang. Estimasi total booking sekarang Rp ".number_format((float) $lockedBooking->estimated_total, 0, ',', '.').'.',
            ]);
        });

        return back()->with('success', 'Pemakaian spare part dicatat, stok dan estimasi total diperbarui.');
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
            'mechanic_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'mechanic')],
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
