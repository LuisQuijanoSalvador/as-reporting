@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Gestión de Usuarios</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Usuarios</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <livewire:usuario-index key="usuario-index-component" />
</div>
@endsection