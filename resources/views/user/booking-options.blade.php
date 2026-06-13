@extends('layouts.app')

@section('title', 'Pilih Booking - MotoCare M')

@section('content')
<section class="page-hero compact">
    <div class="container">
        <p class="eyebrow">Booking Menu</p>
        <h1>PILIH KEBUTUHAN</h1>
        <p class="lead">Tentukan dulu mau service, beli suku cadang, atau sekalian keduanya.</p>
    </div>
</section>

<section class="section container">
    <div class="booking-choice-grid">
        @foreach($options as $option)
            <article class="feature-card booking-choice-card">
                <span class="m-stripe mini"></span>
                <h3>{{ $option['title'] }}</h3>
                <p>{{ $option['description'] }}</p>
                <a class="btn primary service-card-action" href="{{ route('user.booking.create', ['type' => $option['type']]) }}">Pilih Ini</a>
            </article>
        @endforeach
    </div>
</section>
@endsection
