@extends('layouts.app')

@section('title', 'Booking Service - MotoCare M')

@section('content')
<section class="page-hero compact">
    <div class="container">
        <p class="eyebrow">Service Reservation</p>
        <h1>BOOKING SERVICE</h1>
    </div>
</section>

<section class="section container narrow">
    <form class="panel form-panel" method="POST" action="{{ route('user.booking.store') }}">
        @csrf
        <div class="form-grid">
            <label>Jenis Layanan
                <select class="input" name="service_id" required>
                    <option value="">Pilih layanan</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" @selected(old('service_id', $selectedServiceId) == $service->id)>{{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            </label>
            <label>Tanggal Service
                <input class="input" type="date" name="service_date" value="{{ old('service_date') }}" required>
            </label>
            <label>Jam
                <input class="input" type="time" name="service_time" value="{{ old('service_time') }}">
            </label>
            <label>Nama Motor
                <input class="input" type="text" name="motor_name" value="{{ old('motor_name', trim(($profile->motor_brand ?? '') . ' ' . ($profile->motor_type ?? ''))) }}" required>
            </label>
            <label>Plat Nomor
                <input class="input" type="text" name="plate_number" value="{{ old('plate_number', $profile->plate_number ?? '') }}">
            </label>
        </div>
        <label>Keluhan Motor
            <textarea class="input textarea" name="complaint" required>{{ old('complaint') }}</textarea>
        </label>
        <button class="btn primary" type="submit">Kirim Booking</button>
    </form>
</section>
@endsection
