@extends('layouts.app')

@section('title', 'Riwayat Booking - MotoCare M')

@section('content')
<section class="page-hero compact">
    <div class="container">
        <p class="eyebrow">Service History</p>
        <h1>RIWAYAT BOOKING</h1>
    </div>
</section>
<section class="section container">
    <div class="panel">
        @include('user.partials.booking-table', ['bookings' => $bookings])
        {{ $bookings->links() }}
    </div>
</section>
@endsection
