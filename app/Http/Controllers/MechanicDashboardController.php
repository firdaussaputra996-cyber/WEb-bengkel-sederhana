<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ServiceNotification;
use App\Models\SparePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MechanicDashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('mechanic.dashboard', [
            'bookings' => $request->user()->assignedBookings()
                ->with(['user', 'service', 'sparePartUsages.sparePart'])
                ->whereNotIn('status', ['Selesai', 'Dibatalkan'])
                ->orderBy('service_date')
                ->orderBy('service_time')
                ->get(),
            'spareParts' => SparePart::where('is_active', true)->where('stock', '>', 0)->orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless((int) $booking->mechanic_id === (int) $request->user()->id, 403);
        abort_unless(! in_array($booking->status, ['Selesai', 'Dibatalkan'], true), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(['Diproses', 'Selesai'])],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking->update([
            'status' => $data['status'],
            'admin_note' => trim(($booking->admin_note ? $booking->admin_note."\n" : '').($data['note'] ?? '')) ?: $booking->admin_note,
        ]);

        ServiceNotification::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'title' => 'Status servis diperbarui',
            'message' => "Status booking {$booking->booking_code} berubah menjadi {$booking->status}. ".($data['note'] ?? ''),
        ]);

        return back()->with('success', 'Status pengerjaan berhasil diperbarui.');
    }
}
