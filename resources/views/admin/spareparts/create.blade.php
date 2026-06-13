@extends('layouts.app')

@section('title', 'Tambah Suku Cadang - MotoCare M')

@section('content')
<div class="admin-heading">
    <p class="eyebrow">Inventory Form</p>
    <h1>TAMBAH SUKU CADANG</h1>
</div>

@include('admin.spareparts.form', [
    'action' => route('admin.spareparts.store'),
    'method' => 'POST',
    'sparePart' => null,
])
@endsection
