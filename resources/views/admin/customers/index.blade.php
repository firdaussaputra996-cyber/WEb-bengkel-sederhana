@extends('layouts.app')

@section('title', 'Data Pelanggan - MotoCare M')

@section('content')
<div class="admin-heading">
    <p class="eyebrow">Customer Garage</p>
    <h1>DATA PELANGGAN</h1>
</div>

<section class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Nama</th><th>Email</th><th>No. HP</th><th>Motor</th><th>Plat</th><th>Total Booking</th></tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->profile->phone ?? '-' }}</td>
                        <td>{{ trim(($customer->profile->motor_brand ?? '') . ' ' . ($customer->profile->motor_type ?? '')) ?: '-' }}</td>
                        <td>{{ $customer->profile->plate_number ?? '-' }}</td>
                        <td>{{ $customer->bookings_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada pelanggan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $customers->links() }}
</section>
@endsection
