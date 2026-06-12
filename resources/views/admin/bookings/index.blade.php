@extends('layouts.app')

@section('title', 'Manajemen Booking - MotoCare M')

@section('content')
<div class="admin-heading row">
    <div>
        <p class="eyebrow">Service Queue</p>
        <h1>DATA BOOKING</h1>
    </div>
    <div class="actions">
        <a class="btn outline" href="{{ route('admin.bookings.export', 'today') }}">Excel Hari Ini</a>
        <a class="btn outline" href="{{ route('admin.bookings.export', 'month') }}">Cetak Excel Bulan Ini</a>
        <a class="btn outline" href="{{ route('admin.bookings.export', 'all') }}">Cetak Semua Data</a>
        <a class="btn primary" href="{{ route('admin.bookings.create') }}">Tambah Booking</a>
    </div>
</div>
<p class="admin-note">Export hari ini dan bulan ini dihitung dari tanggal service, bukan tanggal booking dibuat.</p>

<section class="panel">
    <form class="filter-row" method="GET">
        <input class="input" name="search" placeholder="Cari kode, user, layanan" value="{{ request('search') }}">
        <select class="input" name="status">
            <option value="">Semua status</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <button class="btn outline" type="submit">Filter</button>
    </form>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Kode</th><th>User</th><th>Layanan</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>{{ $booking->booking_code }}</td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->service->name }}</td>
                        <td>{{ $booking->service_date->format('d M Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                                @csrf
                                @method('PATCH')
                                <select class="input compact" name="status" onchange="this.form.submit()">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" @selected($booking->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="actions">
                            <a class="btn small outline" href="{{ route('admin.bookings.edit', $booking) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" onsubmit="return confirm('Hapus booking ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn small danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $bookings->links() }}
</section>
@endsection
