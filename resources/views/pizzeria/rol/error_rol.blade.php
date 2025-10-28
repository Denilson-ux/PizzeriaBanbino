@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1><i class="fas fa-lock"></i> Acceso denegado</h1>
@stop

@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="card-text">No tienes permiso para acceder a este módulo</h2>
                    <p class="card-text">Por favor, contacta al administrador para obtener acceso.</p>
                    {{-- <a href="{{asset('/')}}" class="btn btn-primary">Volver al inicio</a> --}}
                </div>
            </div>
        </div>
    </div>
</div>

  @stop

@section('css')

@stop

@section('js')

@stop
