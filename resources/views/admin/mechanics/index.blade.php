@extends('layouts.app')

@section('title', 'Akun Mekanik - MotoCare M')

@section('content')
<div class="admin-heading">
    <p class="eyebrow">Mechanic Access</p>
    <h1>AKUN MEKANIK</h1>
</div>

<section class="panel form-panel">
    <h2>Buat akun mekanik</h2>
    <form method="POST" action="{{ route('admin.mechanics.store') }}" class="form-grid">
        @csrf
        <label>Nama<input class="input" name="name" required></label>
        <label>Email login<input class="input" type="email" name="email" required></label>
        <label>Password awal<input class="input" type="password" name="password" minlength="8" required></label>
        <button class="btn primary" type="submit">Tambah Mekanik</button>
    </form>
</section>

<section class="panel" style="margin-top: 1rem">
    <div class="panel-head"><h2>MEKANIK TERDAFTAR</h2></div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Nama</th><th>Email</th><th>Booking Aktif</th></tr></thead>
            <tbody>
                @forelse($mechanics as $mechanic)
                    <tr><td>{{ $mechanic->name }}</td><td>{{ $mechanic->email }}</td><td>{{ $mechanic->assigned_bookings_count }}</td></tr>
                @empty
                    <tr><td colspan="3" class="empty">Belum ada akun mekanik.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
