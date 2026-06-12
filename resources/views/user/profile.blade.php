@extends('layouts.app')

@section('title', 'Profile - MotoCare M')

@section('content')
<section class="page-hero compact">
    <div class="container">
        <p class="eyebrow">Rider Identity</p>
        <h1>PROFILE USER</h1>
    </div>
</section>
<section class="section container narrow">
    <form class="panel form-panel" method="POST" action="{{ route('user.profile.update') }}">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <label>Nama
                <input class="input" name="name" value="{{ old('name', $user->name) }}" required>
            </label>
            <label>No. HP
                <input class="input" name="phone" value="{{ old('phone', $user->profile->phone ?? '') }}">
            </label>
            <label>Alamat
                <input class="input" name="address" value="{{ old('address', $user->profile->address ?? '') }}">
            </label>
            <label>Brand Motor
                <input class="input" name="motor_brand" value="{{ old('motor_brand', $user->profile->motor_brand ?? '') }}">
            </label>
            <label>Tipe Motor
                <input class="input" name="motor_type" value="{{ old('motor_type', $user->profile->motor_type ?? '') }}">
            </label>
            <label>Plat Nomor
                <input class="input" name="plate_number" value="{{ old('plate_number', $user->profile->plate_number ?? '') }}">
            </label>
        </div>
        <button class="btn primary" type="submit">Simpan Profil</button>
    </form>
</section>
@endsection
