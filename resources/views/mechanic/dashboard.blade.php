@extends('layouts.app')

@section('title', 'Antrean Mekanik - MotoCare M')

@section('content')
<section class="page-hero compact">
    <div class="container">
        <p class="eyebrow">Mechanic Workspace</p>
        <h1>ANTREAN SAYA</h1>
        <p class="lead">Booking yang ditugaskan kepada {{ auth()->user()->name }}.</p>
    </div>
</section>

<section class="section container">
    <div class="booking-list">
        @forelse($bookings as $booking)
            <article class="panel">
                <div class="panel-head">
                    <div><p class="eyebrow">{{ $booking->booking_code }}</p><h2>{{ $booking->motor_name }}</h2></div>
                    <span class="badge {{ strtolower($booking->status) }}">{{ $booking->status }}</span>
                </div>
                <div class="detail-list">
                    <div><span>Pelanggan</span><strong>{{ $booking->user->name }}</strong></div>
                    <div><span>Layanan</span><strong>{{ $booking->service->name }}</strong></div>
                    <div><span>Plat Nomor</span><strong>{{ $booking->plate_number ?: '-' }}</strong></div>
                    <div><span>Jadwal</span><strong>{{ $booking->service_date->format('d M Y') }} {{ $booking->service_time }}</strong></div>
                    <div><span>Keluhan</span><strong>{{ $booking->complaint }}</strong></div>
                </div>

                <div class="form-section">
                    <div class="panel-head"><h3>SPARE PART TERPASANG</h3></div>
                    @forelse($booking->sparePartUsages as $usage)
                        <p>{{ $usage->sparePart->name }} × {{ $usage->quantity }} (Rp {{ number_format($usage->unit_price, 0, ',', '.') }}/item)</p>
                    @empty
                        <p class="caption">Belum ada komponen yang dicatat terpasang.</p>
                    @endforelse
                    <form method="POST" action="{{ route('mechanic.bookings.spare-parts', $booking) }}" class="form-grid" style="margin-top: 1rem">
                        @csrf
                        <label>Komponen
                            <select class="input" name="spare_part_id" required>
                                <option value="">Pilih spare part</option>
                                @foreach($spareParts as $part)
                                    <option value="{{ $part->id }}">{{ $part->category }} — {{ $part->name }} (stok {{ $part->stock }})</option>
                                @endforeach
                            </select>
                        </label>
                        <label>Jumlah<input class="input" type="number" name="quantity" min="1" value="1" required></label>
                        <button class="btn outline" type="submit">Catat Terpasang</button>
                    </form>
                </div>

                <form method="POST" action="{{ route('mechanic.bookings.status', $booking) }}" class="form-grid">
                    @csrf
                    @method('PATCH')
                    <label>Update Status
                        <select class="input" name="status" required>
                            <option value="Diproses" @selected($booking->status === 'Diproses')>Diproses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </label>
                    <label>Catatan untuk pelanggan<input class="input" name="note" maxlength="1000" placeholder="Contoh: Kampas rem sudah diganti"></label>
                    <button class="btn primary" type="submit">Simpan Update</button>
                </form>
            </article>
        @empty
            <div class="panel empty">Belum ada booking aktif yang ditugaskan kepada Anda.</div>
        @endforelse
    </div>
</section>
@endsection
