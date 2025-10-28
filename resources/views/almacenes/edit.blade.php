@extends('adminlte::page')

@section('title', 'Editar Almacén')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Editar Almacén: {{ $almacen->nombre }}</h1>
        <div>
            <a href="{{ route('almacenes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
            <a href="{{ route('almacenes.show', $almacen->id_almacen) }}" class="btn btn-info"><i class="fas fa-eye"></i> Ver Detalles</a>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fas fa-edit"></i> Editar Información</div>
            <div class="card-body">
                <form action="{{ route('almacenes.update', $almacen->id_almacen) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre <span class="text-danger">*</span></label>
                                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $almacen->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado">Estado <span class="text-danger">*</span></label>
                                <select id="estado" name="estado" class="form-control @error('estado') is-invalid @enderror" required>
                                    <option value="activo" {{ old('estado', $almacen->estado)=='activo'?'selected':'' }}>Activo</option>
                                    <option value="inactivo" {{ old('estado', $almacen->estado)=='inactivo'?'selected':'' }}>Inactivo</option>
                                </select>
                                @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $almacen->descripcion) }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" value="{{ old('ubicacion', $almacen->ubicacion) }}" class="form-control @error('ubicacion') is-invalid @enderror">
                        @error('ubicacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="responsable">Responsable</label>
                                <input type="text" id="responsable" name="responsable" value="{{ old('responsable', $almacen->responsable) }}" class="form-control @error('responsable') is-invalid @enderror">
                                @error('responsable')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $almacen->telefono) }}" class="form-control @error('telefono') is-invalid @enderror">
                                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
                        <a href="{{ route('almacenes.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-info-circle"></i> Resumen</div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><strong>Ingredientes:</strong> {{ $almacen->total_ingredientes }}</li>
                    <li><strong>Con stock:</strong> {{ $almacen->productos_con_stock }}</li>
                    <li><strong>Stock bajo:</strong> {{ $almacen->productos_stock_bajo }}</li>
                    <li><strong>Valor inventario:</strong> S/. {{ number_format($almacen->valor_total_inventario, 2) }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
