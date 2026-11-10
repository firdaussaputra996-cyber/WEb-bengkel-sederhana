@extends('layouts.app')

@section('title', 'Edit Booking - MotoCare M')

@section('content')
<div class="admin-heading">
    <p class="eyebrow">Queue Control</p>
    <h1>EDIT BOOKING</h1>
</div>
@include('admin.bookings.form', ['booking' => $booking, 'action' => route('admin.bookings.update', $booking), 'method' => 'PUT'])

<section class="panel form-panel" style="margin-top: 1rem">
    <div class="panel-head"><h2>CATAT SPARE PART TERPASANG</h2></div>
    @forelse($booking->sparePartUsages as $usage)
        <p>{{ $usage->sparePart->name }} × {{ $usage->quantity }} — Rp {{ number_format($usage->unit_price, 0, ',', '.') }} per item</p>
    @empty
        <p class="caption">Belum ada spare part yang dicatat terpasang.</p>
    @endforelse
    <form method="POST" action="{{ route('admin.bookings.spare-parts', $booking) }}" class="form-grid" style="margin-top: 1rem">
        @csrf
        <label>Spare part
            <select class="input" name="spare_part_id" required>
                <option value="">Pilih komponen</option>
                @foreach($spareParts as $part)
                    <option value="{{ $part->id }}">{{ $part->category }} — {{ $part->name }} (stok {{ $part->stock }})</option>
                @endforeach
            </select>
        </label>
        <label>Jumlah<input class="input" name="quantity" type="number" min="1" value="1" required></label>
        <button class="btn outline" type="submit">Catat Terpasang</button>
    </form>
</section>
@endsection
