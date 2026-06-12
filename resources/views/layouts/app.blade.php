<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MotoCare M Service')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/motocare.css">
</head>
<body>
    <div class="loader" aria-hidden="true"></div>

    <header class="top-nav">
        <a href="{{ route('landing') }}" class="brand-mark">
            <span class="m-stripe mini"></span>
            <span class="brand-text">MOTOCARE M</span>
        </a>

        <button class="nav-toggle" type="button" data-nav-toggle aria-label="Buka menu">
            <span></span><span></span><span></span>
        </button>

        <nav class="nav-menu" data-nav-menu>
            <a href="{{ route('landing') }}">Home</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.bookings.index') }}">Booking</a>
                    <a href="{{ route('admin.services.index') }}">Service</a>
                    <a href="{{ route('admin.customers.index') }}">Pelanggan</a>
                @else
                    <a href="{{ route('user.dashboard') }}">Dashboard</a>
                    <a href="{{ route('user.services') }}">Layanan</a>
                    <a href="{{ route('user.booking.create') }}">Booking</a>
                    <a href="{{ route('user.history') }}">Riwayat</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-logout" type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a class="nav-cta" href="{{ route('register') }}">Register</a>
            @endauth
        </nav>
    </header>

    @if(auth()->check() && request()->is('admin*'))
        <div class="shell admin-shell">
            <aside class="sidebar">
                <div>
                    <p class="eyebrow">Admin Control</p>
                    <h2>WORKSHOP OPS</h2>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">Data Booking</a>
                <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">Data Service</a>
                <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">Pelanggan</a>
            </aside>
            <main class="content-panel">
                @include('layouts.flash')
                @yield('content')
            </main>
        </div>
    @else
        <main>
            @include('layouts.flash')
            @yield('content')
        </main>
    @endif

    <footer class="footer">
        <div>
            <span class="m-stripe footer-stripe"></span>
            <h3>MOTOCARE M SERVICE</h3>
            <p>Booking service motor dengan pengalaman digital yang cepat, rapi, dan presisi.</p>
        </div>
    </footer>

    <script>
        document.querySelector('[data-nav-toggle]')?.addEventListener('click', () => {
            document.querySelector('[data-nav-menu]')?.classList.toggle('open');
        });
        window.addEventListener('load', () => document.body.classList.add('loaded'));
    </script>
</body>
</html>
