@extends('adminlte::page')

@section('title', 'Crear Ingrediente')

@section('content_header')
    <h1>Crear Nuevo Ingrediente</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('ingredientes.store') }}">
                @csrf
                <div class="row">
                    <!-- Información básica (ancho completo) -->
                    <div class="col-md-12">
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
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="unidad_medida">Unidad de Medida *</label>
                                            <select class="form-control" id="unidad_medida" name="unidad_medida" required>
                                                <option value="">Seleccionar...</option>
                                                <option value="gramos" {{ old('unidad_medida') == 'gramos' ? 'selected' : '' }}>Gramos</option>
                                                <option value="kilogramos" {{ old('unidad_medida') == 'kilogramos' ? 'selected' : '' }}>Kilogramos</option>
                                                <option value="mililitros" {{ old('unidad_medida') == 'mililitros' ? 'selected' : '' }}>Mililitros</option>
                                                <option value="litros" {{ old('unidad_medida') == 'litros' ? 'selected' : '' }}>Litros</option>
                                                <option value="unidades" {{ old('unidad_medida') == 'unidades' ? 'selected' : '' }}>Unidades</option>
                                                <option value="porciones" {{ old('unidad_medida') == 'porciones' ? 'selected' : '' }}>Porciones</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="categoria">Categoría *</label>
                                            <select class="form-control" id="categoria" name="categoria" required>
                                                <option value="">Seleccionar...</option>
                                                <option value="lacteos" {{ old('categoria') == 'lacteos' ? 'selected' : '' }}>Lácteos</option>
                                                <option value="carnes" {{ old('categoria') == 'carnes' ? 'selected' : '' }}>Carnes</option>
                                                <option value="vegetales" {{ old('categoria') == 'vegetales' ? 'selected' : '' }}>Vegetales</option>
                                                <option value="harinas" {{ old('categoria') == 'harinas' ? 'selected' : '' }}>Harinas</option>
                                                <option value="condimentos" {{ old('categoria') == 'condimentos' ? 'selected' : '' }}>Condimentos</option>
                                                <option value="bebidas" {{ old('categoria') == 'bebidas' ? 'selected' : '' }}>Bebidas</option>
                                                <option value="otros" {{ old('categoria') == 'otros' ? 'selected' : '' }}>Otros</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="es_perecedero" name="es_perecedero" value="1" {{ old('es_perecedero') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="es_perecedero">
                                                    Es perecedero
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" id="dias_vencimiento_group" style="display: none;">
                                            <label for="dias_vencimiento">Días hasta vencimiento</label>
                                            <input type="number" class="form-control" id="dias_vencimiento" name="dias_vencimiento" value="{{ old('dias_vencimiento') }}" min="1">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="estado">Estado *</label>
                                    <select class="form-control" id="estado" name="estado" required>
                                        <option value="activo" {{ old('estado', 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Ingrediente
                                </button>
                                <a href="{{ route('ingredientes.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
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
        // Mostrar/ocultar días de vencimiento
        $('#es_perecedero').change(function() {
            if ($(this).is(':checked')) {
                $('#dias_vencimiento_group').show();
                $('#dias_vencimiento').attr('required', true);
            } else {
                $('#dias_vencimiento_group').hide();
                $('#dias_vencimiento').attr('required', false);
                $('#dias_vencimiento').val('');
            }
        });
        
        // Verificar estado inicial
        if ($('#es_perecedero').is(':checked')) {
            $('#dias_vencimiento_group').show();
        }
    });
</script>
@endsection