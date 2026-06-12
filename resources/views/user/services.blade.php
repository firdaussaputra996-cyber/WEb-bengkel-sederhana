@extends('layouts.app')

@section('title', 'Layanan - MotoCare M')

@section('content')
<section class="page-hero compact">
    <div class="container">
        <p class="eyebrow">Workshop Menu</p>
        <h1>DATA SERVICE</h1>
    </div>
</section>
<section class="section container">
    <div class="card-grid">
        @foreach($services as $service)
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
                <a class="btn small outline service-card-action" href="{{ route('user.booking.create', ['service_id' => $service->id]) }}">Booking Layanan</a>
            </article>
        @endforeach
    </div>
    {{ $services->links() }}
</section>
@endsection
