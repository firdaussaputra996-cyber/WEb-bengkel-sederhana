@extends('layouts.app')

@section('title', 'Tambah Service - MotoCare M')

@section('content')
<div class="admin-heading">
    <p class="eyebrow">New Service</p>
    <h1>TAMBAH SERVICE</h1>
</div>
@include('admin.services.form', ['service' => null, 'action' => route('admin.services.store'), 'method' => 'POST'])
@endsection
