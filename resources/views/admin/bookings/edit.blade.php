@extends('layouts.app')

@section('title', 'Edit Booking - MotoCare M')

@section('content')
<div class="admin-heading">
    <p class="eyebrow">Queue Control</p>
    <h1>EDIT BOOKING</h1>
</div>
@include('admin.bookings.form', ['booking' => $booking, 'action' => route('admin.bookings.update', $booking), 'method' => 'PUT'])
@endsection
