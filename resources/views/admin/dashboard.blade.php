@extends('layouts.app')

@section('title', 'Admin Dashboard - MotoCare M')

@section('content')
<div class="admin-heading">
    <p class="eyebrow">Workshop Intelligence</p>
    <h1>DASHBOARD ADMIN</h1>
</div>

<div class="stats-grid four">
    <div class="spec-cell"><strong>{{ $usersCount }}</strong><span>Jumlah User</span></div>
    <div class="spec-cell"><strong>{{ $bookingsCount }}</strong><span>Total Booking</span></div>
    <div class="spec-cell"><strong>{{ $doneCount }}</strong><span>Booking Selesai</span></div>
    <div class="spec-cell"><strong>{{ $pendingCount }}</strong><span>Booking Pending</span></div>
</div>

<div class="dashboard-grid">
    <section class="panel">
        <div class="panel-head">
            <h2>STATUS BOOKING</h2>
        </div>
        <div class="chart-bars">
            @php($max = max($chartData) ?: 1)
            @foreach($chartData as $label => $value)
                <div class="bar-row">
                    <span>{{ $label }}</span>
                    <div class="bar-track"><div style="width: {{ ($value / $max) * 100 }}%"></div></div>
                    <strong>{{ $value }}</strong>
                </div>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>BOOKING TERBARU</h2>
            <a class="text-link" href="{{ route('admin.bookings.index') }}">Kelola</a>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Kode</th><th>User</th><th>Service</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($latestBookings as $booking)
                        <tr>
                            <td>{{ $booking->booking_code }}</td>
                            <td>{{ $booking->user->name }}</td>
                            <td>{{ $booking->service->name }}</td>
                            <td><span class="badge {{ strtolower($booking->status) }}">{{ $booking->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
