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
                @auth
                    @if(auth()->user()->isAdmin())
                        <a class="btn small outline service-card-action" href="{{ route('admin.services.edit', $service) }}">Kelola Service</a>
                    @else
                        <a class="btn small outline service-card-action" href="{{ route('user.booking.create', ['service_id' => $service->id]) }}">Booking Layanan</a>
                    @endif
                @else
                    <a class="btn small outline service-card-action" href="{{ route('login') }}">Login untuk Booking</a>
                @endauth
            </article>
        @empty
            <article class="feature-card">
                <p>Layanan akan tampil setelah admin menambahkan data service.</p>
            </article>
        @endforelse
    </div>
</section>

<section class="section container sparepart-feature">
    <div class="sparepart-feature-media" role="img" aria-label="Pilihan oli dan suku cadang motor"></div>
    <div class="sparepart-feature-copy">
        <p class="eyebrow">Parts Counter</p>
        <h2>SUKU CADANG & OLI TERSEDIA</h2>
        <p>Pilih oli mesin, busi, kampas rem, filter udara, atau komponen matic yang ingin disiapkan sebelum datang ke bengkel.</p>
        <div class="hero-actions">
            <a class="btn primary" href="{{ route('spareparts.index') }}">Lihat Suku Cadang</a>
            @auth
                @if(! auth()->user()->isAdmin())
                    <a class="btn outline" href="{{ route('user.booking.create', ['type' => 'sparepart']) }}">Booking Pembelian</a>
                @else
                    <a class="btn outline" href="{{ route('admin.spareparts.index') }}">Kelola Stok</a>
                @endif
            @else
                <a class="btn outline" href="{{ route('login') }}">Login untuk Beli</a>
            @endauth
        </div>
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
