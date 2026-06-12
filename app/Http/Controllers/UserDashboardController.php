<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $selectedServiceId = Service::where('is_active', true)
            ->whereKey($request->integer('service_id'))
            ->value('id');

        return view('user.booking', [
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
            'profile' => Auth::user()->profile,
            'selectedServiceId' => $selectedServiceId,
        ]);
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'service_date' => ['required', 'date', 'after_or_equal:today'],
            'service_time' => ['nullable', 'date_format:H:i'],
            'motor_name' => ['required', 'string', 'max:120'],
            'plate_number' => ['nullable', 'string', 'max:30'],
            'complaint' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $data['user_id'] = Auth::id();
        $data['booking_code'] = 'MC-' . now()->format('ymd') . '-' . str_pad((string) (Booking::count() + 1), 4, '0', STR_PAD_LEFT);

        Booking::create($data);

        return redirect()->route('user.history')->with('success', 'Booking service berhasil dibuat.');
    }

    public function history(): View
    {
        return view('user.history', [
            'bookings' => Auth::user()->bookings()->with('service')->latest()->paginate(10),
        ]);
    }

    public function services(): View
    {
        return view('user.services', [
            'services' => Service::where('is_active', true)->latest()->paginate(9),
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
}
