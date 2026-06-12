@extends('layouts.app')

@section('title', 'MotoCare M Service')

@section('content')
<section class="hero-photo">
    <div class="hero-overlay">
        <p class="eyebrow">Premium Motorcycle Workshop</p>
        <h1>BOOKING SERVICE BENGKEL MOTOR</h1>
        <p class="lead">Atur jadwal, pilih layanan, dan pantau status pengerjaan motor dalam satu dashboard yang presisi.</p>
        <div class="hero-actions">
            @guest
                <a class="btn primary" href="{{ route('register') }}">Mulai Booking</a>
                <a class="btn outline" href="{{ route('login') }}">Masuk</a>
            @else
                @if(auth()->user()->isAdmin())
                    <a class="btn primary" href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
                    <a class="btn outline" href="{{ route('admin.bookings.index') }}">Kelola Booking</a>
                @else
                    <a class="btn primary" href="{{ route('user.booking.create') }}">Mulai Booking</a>
                    <a class="btn outline" href="{{ route('user.dashboard') }}">Dashboard</a>
                @endif
            @endguest
        </div>
    </div>
</section>

<section class="section container">
    <div class="section-heading">
        <p class="eyebrow">Fast Service System</p>
        <h2>LAYANAN BENGKEL</h2>
        <p>Daftar layanan aktif untuk servis harian, performa, keamanan, dan diagnosa motor.</p>
    </div>
    <div class="card-grid">
        @forelse($services as $service)
            @php($photoKey = \Illuminate\Support\Str::slug($service->category ?: 'general'))
            <article class="feature-card">
                <div class="card-photo service-photo service-{{ $photoKey }}"></div>
                <p class="eyebrow">{{ $service->category }}</p>
                <h3>{{ $service->name }}</h3>
                <p>{{ $service->description }}</p>
                <div class="card-meta">
                    <span>{{ $service->duration_minutes }} menit</span>
                    <strong>Rp {{ number_format($service->price, 0, ',', '.') }}</strong>
                </div>
                <a class="btn small outline service-card-action" href="{{ route('user.booking.create', ['service_id' => $service->id]) }}">Booking Layanan</a>
            </article>
        @empty
            <article class="feature-card">
                <p>Layanan akan tampil setelah admin menambahkan data service.</p>
            </article>
        @endforelse
    </div>
</section>

<section class="photo-band">
    <div>
        <span class="m-stripe"></span>
        <h2>MONITOR STATUS SERVICE SECARA REAL TIME</h2>
        <p>Menunggu, Diproses, sampai Selesai. Semua riwayat booking tersimpan rapi untuk pelanggan dan admin.</p>
    </div>
</section>
@endsection
