@extends('adminlte::page')

@section('title', 'Detalle del Ingrediente')

@section('content_header')
    <h1>{{ $ingrediente->nombre }} - Detalle del Ingrediente</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Información básica -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-seedling"></i> Información del Ingrediente
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('ingredientes.edit', $ingrediente->id_ingrediente) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="{{ route('ingredientes.index') }}" class="btn btn-info">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nombre:</strong></td>
                                    <td>{{ $ingrediente->nombre }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Categoría:</strong></td>
                                    <td><span class="badge badge-info">{{ ucfirst($ingrediente->categoria ?? 'Otros') }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Unidad de Medida:</strong></td>
                                    <td><span class="badge badge-secondary">{{ $ingrediente->unidad_medida }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Es Perecedero:</strong></td>
                                    <td>
                                        @if($ingrediente->es_perecedero)
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Sí</span>
                                            @if($ingrediente->dias_vencimiento)
                                                <br><small class="text-muted">Vence en {{ $ingrediente->dias_vencimiento }} días</small>
                                            @endif
                                        @else
                                            <span class="badge badge-success"><i class="fas fa-infinity"></i> No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        @if($ingrediente->estado == 'activo')
                                            <span class="badge badge-success">Activo</span>
                                        @else
                                            <span class="badge badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Fecha de Creación:</strong></td>
                                    <td>{{ $ingrediente->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Última Actualización:</strong></td>
                                    <td>{{ $ingrediente->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($ingrediente->descripcion)
                        <div class="alert alert-info">
                            <strong>Descripción:</strong> {{ $ingrediente->descripcion }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection