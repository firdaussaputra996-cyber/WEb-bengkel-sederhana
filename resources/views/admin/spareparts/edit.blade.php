@extends('layouts.app')

@section('title', 'Edit Suku Cadang - MotoCare M')

@section('content')
<div class="admin-heading">
    <p class="eyebrow">Inventory Form</p>
    <h1>EDIT SUKU CADANG</h1>
</div>

@include('admin.spareparts.form', [
    'action' => route('admin.spareparts.update', $sparePart),
    'method' => 'PUT',
    'sparePart' => $sparePart,
])
@endsection
