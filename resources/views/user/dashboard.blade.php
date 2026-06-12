@extends('layouts.app')

@section('title', 'Dashboard User - MotoCare M')

@section('content')
<section class="page-hero compact">
    <div class="container">
        <p class="eyebrow">Rider Dashboard</p>
        <h1>DASHBOARD USER</h1>
        <p class="lead">Halo {{ auth()->user()->name }}, pantau booking dan pilih service berikutnya.</p>
        <a class="btn primary" href="{{ route('user.booking.create') }}">Booking Service</a>
    </div>
</section>

<section class="section container">
    <div class="stats-grid">
        <div class="spec-cell"><strong>{{ $servicesCount }}</strong><span>Layanan Aktif</span></div>
        <div class="spec-cell"><strong>{{ $pendingCount }}</strong><span>Menunggu</span></div>
        <div class="spec-cell"><strong>{{ $doneCount }}</strong><span>Selesai</span></div>
    </div>

    @if($notifications->isNotEmpty())
        <div class="panel notification-panel">
            <div class="panel-head">
                <h2>PESAN SERVICE</h2>
            </div>
            <div class="notification-list">
                @foreach($notifications as $notification)
                    <article class="notification-item">
                        <span class="m-stripe mini"></span>
                        <div>
                            <h3>{{ $notification->title }}</h3>
                            <p>{{ $notification->message }}</p>
                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif

    <div class="panel">
        <div class="panel-head">
            <h2>BOOKING TERBARU</h2>
            <a class="text-link" href="{{ route('user.history') }}">Lihat Semua</a>
        </div>
        @include('user.partials.booking-table', ['bookings' => $bookings])
    </div>
</section>
@endsection
