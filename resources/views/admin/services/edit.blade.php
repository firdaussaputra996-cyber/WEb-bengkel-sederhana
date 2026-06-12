@extends('layouts.app')

@section('title', 'Edit Service - MotoCare M')

@section('content')
<div class="admin-heading">
    <p class="eyebrow">Service Tuning</p>
    <h1>EDIT SERVICE</h1>
</div>
@include('admin.services.form', ['service' => $service, 'action' => route('admin.services.update', $service), 'method' => 'PUT'])
@endsection
