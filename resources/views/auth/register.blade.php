@extends('layouts.app')

@section('title', 'Register - MotoCare M')

@section('content')
<section class="auth-screen">
    <form class="auth-card wide" method="POST" action="{{ route('register.store') }}">
        @csrf
        <span class="m-stripe"></span>
        <p class="eyebrow">New Rider Profile</p>
        <h1>REGISTER</h1>
        <div class="form-grid">
            <label>Nama
                <input class="input" type="text" name="name" value="{{ old('name') }}" required>
            </label>
            <label>Email
                <input class="input" type="email" name="email" value="{{ old('email') }}" required>
            </label>
            <label>Password
                <input class="input" type="password" name="password" required>
            </label>
            <label>Konfirmasi Password
                <input class="input" type="password" name="password_confirmation" required>
            </label>
            <label>No. HP
                <input class="input" type="text" name="phone" value="{{ old('phone') }}">
            </label>
            <label>Tipe Motor
                <input class="input" type="text" name="motor_type" value="{{ old('motor_type') }}">
            </label>
            <label>Plat Nomor
                <input class="input" type="text" name="plate_number" value="{{ old('plate_number') }}">
            </label>
        </div>
        <button class="btn primary full" type="submit">Buat Akun</button>
        <p class="auth-link">Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
    </form>
</section>
@endsection
