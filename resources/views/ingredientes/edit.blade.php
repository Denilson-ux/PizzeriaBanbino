@extends('adminlte::page')

@section('title', 'Editar Ingrediente')

@section('content_header')
    <h1>Editar Ingrediente: {{ $ingrediente->nombre }}</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('ingredientes.update', $ingrediente->id_ingrediente) }}">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-seedling"></i> Información del Ingrediente
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="nombre">Nombre del Ingrediente *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $ingrediente->nombre) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $ingrediente->descripcion) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unidad_medida">Unidad de Medida *</label>
                                    <select class="form-control" id="unidad_medida" name="unidad_medida" required>
                                        <option value="gramos" {{ old('unidad_medida', $ingrediente->unidad_medida) == 'gramos' ? 'selected' : '' }}>Gramos</option>
                                        <option value="kilogramos" {{ old('unidad_medida', $ingrediente->unidad_medida) == 'kilogramos' ? 'selected' : '' }}>Kilogramos</option>
                                        <option value="mililitros" {{ old('unidad_medida', $ingrediente->unidad_medida) == 'mililitros' ? 'selected' : '' }}>Mililitros</option>
                                        <option value="litros" {{ old('unidad_medida', $ingrediente->unidad_medida) == 'litros' ? 'selected' : '' }}>Litros</option>
                                        <option value="unidades" {{ old('unidad_medida', $ingrediente->unidad_medida) == 'unidades' ? 'selected' : '' }}>Unidades</option>
                                        <option value="porciones" {{ old('unidad_medida', $ingrediente->unidad_medida) == 'porciones' ? 'selected' : '' }}>Porciones</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="categoria">Categoría *</label>
                                    <select class="form-control" id="categoria" name="categoria" required>
                                        <option value="lacteos" {{ old('categoria', $ingrediente->categoria) == 'lacteos' ? 'selected' : '' }}>Lácteos</option>
                                        <option value="carnes" {{ old('categoria', $ingrediente->categoria) == 'carnes' ? 'selected' : '' }}>Carnes</option>
                                        <option value="vegetales" {{ old('categoria', $ingrediente->categoria) == 'vegetales' ? 'selected' : '' }}>Vegetales</option>
                                        <option value="harinas" {{ old('categoria', $ingrediente->categoria) == 'harinas' ? 'selected' : '' }}>Harinas</option>
                                        <option value="condimentos" {{ old('categoria', $ingrediente->categoria) == 'condimentos' ? 'selected' : '' }}>Condimentos</option>
                                        <option value="bebidas" {{ old('categoria', $ingrediente->categoria) == 'bebidas' ? 'selected' : '' }}>Bebidas</option>
                                        <option value="otros" {{ old('categoria', $ingrediente->categoria) == 'otros' ? 'selected' : '' }}>Otros</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="es_perecedero" name="es_perecedero" value="1" {{ old('es_perecedero', $ingrediente->es_perecedero) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="es_perecedero">
                                            Es perecedero
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="dias_vencimiento_group">
                                    <label for="dias_vencimiento">Días hasta vencimiento</label>
                                    <input type="number" class="form-control" id="dias_vencimiento" name="dias_vencimiento" value="{{ old('dias_vencimiento', $ingrediente->dias_vencimiento) }}" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado *</label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="activo" {{ old('estado', $ingrediente->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado', $ingrediente->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Ingrediente
                        </button>
                        <a href="{{ route('ingredientes.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <a href="{{ route('ingredientes.show', $ingrediente->id_ingrediente) }}" class="btn btn-info ml-2">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(document).ready(function() {
        function toggleDias() {
            if ($('#es_perecedero').is(':checked')) {
                $('#dias_vencimiento_group').show();
            } else {
                $('#dias_vencimiento_group').hide();
                $('#dias_vencimiento').val('');
            }
        }
        toggleDias();
        $('#es_perecedero').change(toggleDias);
    });
</script>
@endsection