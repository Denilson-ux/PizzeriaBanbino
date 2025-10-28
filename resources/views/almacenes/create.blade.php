@extends('adminlte::page')

@section('title', 'Crear Almacén')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Crear Nuevo Almacén</h1>
        <a href="{{ route('almacenes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Almacenes
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-warehouse me-2"></i>Información del Almacén</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('almacenes.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre del Almacén <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Almacén Principal" required>
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado">Estado <span class="text-danger">*</span></label>
                                <select class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                    <option value="activo" {{ old('estado', 'activo')=='activo'?'selected':'' }}>Activo</option>
                                    <option value="inactivo" {{ old('estado')=='inactivo'?'selected':'' }}>Inactivo</option>
                                </select>
                                @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" placeholder="Descripción del almacén y su propósito">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="ubicacion">Ubicación Física</label>
                        <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}" placeholder="Ej: Av. Principal 123, Lima">
                        @error('ubicacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="responsable">Responsable</label>
                                <input type="text" class="form-control @error('responsable') is-invalid @enderror" id="responsable" name="responsable" value="{{ old('responsable') }}" placeholder="Nombre del responsable">
                                @error('responsable')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono de contacto">
                                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Crear Almacén</button>
                        <a href="{{ route('almacenes.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0"><i class="fas fa-info-circle text-info me-2"></i>Información Importante</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-warehouse text-primary"></i> Sobre los Almacenes:</h6>
                        <ul class="mb-0">
                            <li>Representan ubicaciones físicas para almacenar ingredientes</li>
                            <li>Cada almacén mantiene inventario independiente</li>
                            <li>Las compras requieren seleccionar almacén destino</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-shopping-cart text-warning"></i> Flujo de Compras:</h6>
                        <ul class="mb-0">
                            <li>Crear almacén físico</li>
                            <li>Realizar compra seleccionando almacén destino</li>
                            <li>Completar compra para aplicar al inventario del almacén</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
