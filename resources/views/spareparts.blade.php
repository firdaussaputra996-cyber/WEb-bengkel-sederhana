@extends('layouts.app')

@section('title', 'Suku Cadang - MotoCare M')

@section('content')
<section class="page-hero compact sparepart-hero">
    <div class="container">
        <p class="eyebrow">Parts Catalog</p>
        <h1>SUKU CADANG</h1>
        <p class="lead">Daftar oli dan komponen yang bisa dipesan sebelum datang ke bengkel.</p>
    </div>
</section>

<section class="section container">
    @foreach($addonGroups as $category => $items)
        <div class="section-heading">
            <p class="eyebrow">{{ $category }}</p>
            <h2>{{ strtoupper($category) }}</h2>
        </div>
        <div class="parts-grid">
            @foreach($items as $item)
                <article class="part-card">
                    <div>
                        <p class="eyebrow">{{ $category }}</p>
                        <h3>{{ $item['name'] }}</h3>
                        @if(! empty($item['description']))
                            <p>{{ $item['description'] }}</p>
                        @endif
                    </div>
                    <div class="part-card-meta">
                        <strong>Rp {{ number_format($item['price'], 0, ',', '.') }}</strong>
                        <span>Stok {{ $item['stock'] }}</span>
                    </div>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a class="btn small outline" href="{{ route('admin.spareparts.index') }}">Kelola Stok</a>
                        @else
                            <a class="btn small primary" href="{{ route('user.booking.create', ['type' => 'sparepart', 'addon' => $item['key']]) }}">Pesan</a>
                        @endif
                    @else
                        <a class="btn small outline" href="{{ route('login') }}">Login untuk Pesan</a>
                    @endauth
                </article>
            @endforeach
        </div>
    @endforeach
</section>
@endsection
