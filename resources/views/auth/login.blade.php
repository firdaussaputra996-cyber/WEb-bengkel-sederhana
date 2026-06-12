@extends('layouts.app')

@section('title', 'Login - MotoCare M')

@section('content')
<section class="auth-screen">
    <form class="auth-card" method="POST" action="{{ route('login.store') }}">
        @csrf
        <span class="m-stripe"></span>
        <p class="eyebrow">Secure Access</p>
        <h1>LOGIN</h1>
        <label>Email
            <input class="input" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </label>
        <label>Password
            <input class="input" type="password" name="password" required>
        </label>
        <label class="check-line">
            <input type="checkbox" name="remember" value="1"> Ingat saya
        </label>
        <button class="btn primary full" type="submit">Masuk</button>
        <p class="auth-link">Belum punya akun? <a href="{{ route('register') }}">Register</a></p>
        <p class="caption">Demo: admin@motocare.test / user@motocare.test, password default factory.</p>
    </form>
</section>
@endsection
