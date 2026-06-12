@extends('layouts.app')

@section('title', 'Tambah Booking - MotoCare M')

@section('content')
<div class="admin-heading">
    <p class="eyebrow">Manual Reservation</p>
    <h1>TAMBAH BOOKING</h1>
</div>
@include('admin.bookings.form', ['booking' => null, 'action' => route('admin.bookings.store'), 'method' => 'POST'])
@endsection
