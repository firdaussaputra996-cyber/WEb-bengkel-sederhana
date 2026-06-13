@extends('layouts.app')

@section('title', 'Detail Booking - MotoCare M')

@section('content')
<section class="page-hero compact">
    <div class="container">
        <p class="eyebrow">Booking Detail</p>
        <h1>{{ $booking->booking_code }}</h1>
        <p class="lead">{{ $booking->booking_type }} · {{ $booking->service_date->format('d M Y') }} {{ $booking->service_time }}</p>
    </div>
</section>

<section class="section container">
    <div class="detail-grid">
        <article class="panel">
            <div class="panel-head">
                <h2>RINGKASAN</h2>
                <span class="badge {{ strtolower($booking->status) }}">{{ $booking->status }}</span>
            </div>
            <div class="detail-list">
                <div><span>Layanan</span><strong>{{ $booking->service->name }}</strong></div>
                <div><span>Motor</span><strong>{{ $booking->motor_name }}</strong></div>
                <div><span>Tanggal</span><strong>{{ $booking->service_date->format('d M Y') }}</strong></div>
                <div><span>Jam</span><strong>{{ $booking->service_time ?: '-' }}</strong></div>
                <div><span>Total</span><strong>Rp {{ number_format($booking->estimated_total ?: $booking->service->price, 0, ',', '.') }}</strong></div>
                <div><span>Pembayaran</span><strong>{{ $booking->payment_status }}</strong></div>
            </div>
        </article>

        <article class="panel">
            <div class="panel-head">
                <h2>PEMBAYARAN</h2>
            </div>
            <div class="detail-list">
                <div><span>Metode</span><strong>{{ $booking->payment_method ?: '-' }}</strong></div>
                <div><span>Referensi</span><strong>{{ $booking->payment_reference ?: '-' }}</strong></div>
            </div>
            @if($booking->payment_proof)
                <a class="btn small outline" href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank" rel="noopener">Lihat Bukti</a>
            @endif
            @if($booking->payment_status !== 'Lunas' && $booking->status !== 'Dibatalkan')
                <form class="proof-upload detail-upload" method="POST" action="{{ route('user.booking.payment-proof', $booking) }}" enctype="multipart/form-data">
                    @csrf
                    <input class="input compact" type="file" name="payment_proof" accept="image/*" required>
                    <button class="btn small primary" type="submit">{{ $booking->payment_proof ? 'Upload Ulang Bukti' : 'Upload Bukti Bayar' }}</button>
                </form>
            @endif
        </article>
    </div>

    <div class="detail-grid">
        <article class="panel">
            <div class="panel-head">
                <h2>KELUHAN / CATATAN</h2>
            </div>
            <p>{{ $booking->complaint }}</p>
            @if($booking->admin_note)
                <div class="proof-panel">
                    <span>Catatan Admin</span>
                    <p>{{ $booking->admin_note }}</p>
                </div>
            @endif
        </article>

        <article class="panel">
            <div class="panel-head">
                <h2>OLI & SUKU CADANG</h2>
            </div>
            @if(collect($booking->selected_addons ?? [])->isNotEmpty())
                <div class="detail-list">
                    @foreach($booking->selected_addons as $addon)
                        <div>
                            <span>{{ $addon['category'] ?? 'Add-on' }}</span>
                            <strong>{{ $addon['name'] ?? '-' }} · Rp {{ number_format($addon['price'] ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="caption">Tidak ada add-on.</p>
            @endif
        </article>
    </div>

    <div class="actions detail-actions">
        <a class="btn outline" href="{{ route('user.history') }}">Kembali</a>
        @if($booking->status === 'Menunggu')
            <form method="POST" action="{{ route('user.booking.cancel', $booking) }}" onsubmit="return confirm('Batalkan booking ini?')">
                @csrf
                @method('PATCH')
                <button class="btn danger" type="submit">Batalkan Booking</button>
            </form>
        @endif
    </div>
</section>
@endsection
