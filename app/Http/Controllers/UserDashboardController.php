<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\SparePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('user.dashboard', [
            'bookings' => $user->bookings()->with('service')->latest()->take(5)->get(),
            'notifications' => $user->serviceNotifications()->with(['booking.service'])->latest()->take(5)->get(),
            'servicesCount' => Service::where('is_active', true)->count(),
            'pendingCount' => $user->bookings()->where('status', 'Menunggu')->count(),
            'doneCount' => $user->bookings()->where('status', 'Selesai')->count(),
        ]);
    }

    public function createBooking(Request $request): View
    {
        $selectedBookingType = $this->selectedBookingType($request);

        if (! $selectedBookingType && ! $request->filled('service_id')) {
            return view('user.booking-options', [
                'options' => [
                    [
                        'title' => 'Service Motor',
                        'type' => 'service',
                        'description' => 'Booking jadwal service rutin, tune up, CVT, rem, kelistrikan, atau perbaikan lain.',
                    ],
                    [
                        'title' => 'Beli Suku Cadang',
                        'type' => 'sparepart',
                        'description' => 'Pesan oli atau sparepart dulu, lalu ambil atau pasang di bengkel sesuai jadwal.',
                    ],
                    [
                        'title' => 'Service + Beli',
                        'type' => 'bundle',
                        'description' => 'Service motor sekaligus pilih oli atau sparepart yang ingin disiapkan.',
                    ],
                ],
            ]);
        }

        $selectedBookingType ??= 'Service';

        $selectedServiceId = Service::where('is_active', true)
            ->whereKey($request->integer('service_id'))
            ->value('id');

        if ($selectedBookingType === 'Beli Suku Cadang') {
            $selectedServiceId = Service::where('is_active', true)
                ->where('name', 'Pembelian Suku Cadang')
                ->value('id');
        }

        $serviceOptions = Service::where('is_active', true)
            ->where('category', '!=', 'Sparepart')
            ->orderBy('name')
            ->get();

        return view('user.booking', [
            'services' => $serviceOptions,
            'profile' => Auth::user()->profile,
            'selectedServiceId' => $selectedServiceId,
            'selectedBookingType' => $selectedBookingType,
            'preselectedAddons' => $request->filled('addon') ? [$request->query('addon')] : [],
            'bookingTypes' => Booking::BOOKING_TYPES,
            'paymentMethods' => Booking::PAYMENT_METHODS,
            'addonGroups' => SparePart::groupedOptions(),
        ]);
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'booking_type' => ['required', Rule::in(Booking::BOOKING_TYPES)],
            'service_date' => ['required', 'date', 'after_or_equal:today'],
            'service_time' => ['nullable', 'date_format:H:i'],
            'motor_name' => ['nullable', 'required_unless:booking_type,Beli Suku Cadang', 'string', 'max:120'],
            'plate_number' => ['nullable', 'string', 'max:30'],
            'complaint' => ['nullable', 'required_unless:booking_type,Beli Suku Cadang', 'string', 'min:10', 'max:1000'],
            'selected_addons' => ['nullable', 'array'],
            'selected_addons.*' => ['string'],
            'addon_quantities' => ['nullable', 'array'],
            'addon_quantities.*' => ['integer', 'min:1', 'max:99'],
            'payment_method' => ['required', Rule::in(Booking::PAYMENT_METHODS)],
            'payment_reference' => ['nullable', 'string', 'max:120'],
        ]);

        if ($data['booking_type'] === 'Beli Suku Cadang' && empty($data['selected_addons'])) {
            return back()
                ->withErrors(['selected_addons' => 'Pilih minimal satu oli atau suku cadang untuk booking pembelian.'])
                ->withInput();
        }

        $service = Service::findOrFail($data['service_id']);
        $selectedAddons = SparePart::selectedAddons($data['selected_addons'] ?? [], $data['addon_quantities'] ?? []);
        $addonTotal = collect($selectedAddons)->sum('line_total');

        $data['selected_addons'] = $selectedAddons;
        unset($data['addon_quantities']);
        $data['addon_total'] = $addonTotal;
        $data['estimated_total'] = $service->price + $addonTotal;
        $data['payment_status'] = 'Menunggu Pembayaran';
        $data['motor_name'] = $data['motor_name'] ?: 'Pembelian Suku Cadang';
        $data['plate_number'] = null;
        $data['complaint'] = $data['complaint'] ?: 'Tidak ada catatan tambahan.';
        $data['user_id'] = Auth::id();
        $data['booking_code'] = 'MC-' . now()->format('ymd') . '-' . str_pad((string) (Booking::count() + 1), 4, '0', STR_PAD_LEFT);

        $booking = Booking::create($data);

        return redirect()->route('user.booking.show', $booking)->with('success', 'Booking berhasil dibuat. Silakan lakukan pembayaran online sesuai metode yang dipilih.');
    }

    public function history(): View
    {
        return view('user.history', [
            'bookings' => Auth::user()->bookings()->with('service')->latest()->paginate(10),
        ]);
    }

    public function showBooking(Booking $booking): View
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        return view('user.booking-detail', [
            'booking' => $booking->load('service'),
        ]);
    }

    public function cancelBooking(Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        if ($booking->status !== 'Menunggu') {
            return back()->with('error', 'Booking hanya bisa dibatalkan saat status masih Menunggu.');
        }

        $booking->update([
            'status' => 'Dibatalkan',
            'admin_note' => trim(($booking->admin_note ? $booking->admin_note . "\n" : '') . 'Dibatalkan oleh pelanggan.'),
        ]);

        return redirect()->route('user.history')->with('success', 'Booking berhasil dibatalkan.');
    }

    public function uploadPaymentProof(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $data = $request->validate([
            'payment_proof' => ['required', 'image', 'max:2048'],
        ]);

        if ($booking->payment_proof) {
            Storage::disk('public')->delete($booking->payment_proof);
        }

        $booking->update([
            'payment_proof' => $data['payment_proof']->store('payment-proofs', 'public'),
            'payment_status' => 'Menunggu Verifikasi',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Admin akan memverifikasi pembayaran.');
    }

    public function services(): View
    {
        return view('user.services', [
            'services' => Service::where('is_active', true)
                ->where('category', '!=', 'Sparepart')
                ->latest()
                ->paginate(9),
        ]);
    }

    public function profile(): View
    {
        return view('user.profile', [
            'user' => Auth::user()->load('profile'),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'motor_brand' => ['nullable', 'string', 'max:80'],
            'motor_type' => ['nullable', 'string', 'max:100'],
            'plate_number' => ['nullable', 'string', 'max:30'],
        ]);

        $request->user()->update(['name' => $data['name']]);
        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            collect($data)->except('name')->toArray()
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    private function selectedBookingType(Request $request): ?string
    {
        return match ($request->query('type')) {
            'service' => 'Service',
            'sparepart' => 'Beli Suku Cadang',
            'bundle' => 'Service + Suku Cadang',
            default => in_array($request->query('type'), Booking::BOOKING_TYPES, true) ? $request->query('type') : null,
        };
    }
}
