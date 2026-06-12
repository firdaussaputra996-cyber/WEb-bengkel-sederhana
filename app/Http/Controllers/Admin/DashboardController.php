<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $statusCounts = Booking::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.dashboard', [
            'usersCount' => User::where('role', 'user')->count(),
            'bookingsCount' => Booking::count(),
            'doneCount' => Booking::where('status', 'Selesai')->count(),
            'pendingCount' => Booking::where('status', 'Menunggu')->count(),
            'latestBookings' => Booking::with(['user', 'service'])->latest()->take(8)->get(),
            'chartData' => [
                'Menunggu' => $statusCounts['Menunggu'] ?? 0,
                'Diproses' => $statusCounts['Diproses'] ?? 0,
                'Selesai' => $statusCounts['Selesai'] ?? 0,
            ],
        ]);
    }
}
