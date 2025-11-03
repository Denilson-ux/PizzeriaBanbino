@extends('adminlte::page')

@section('title', 'Gestionar receta')

@section('content_header')
    <h1>Gestionar receta: {{ $itemMenu->nombre }}</h1>
@stop

@section('content')
<div class="container-fluid">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Agregar ingrediente</div>
        <div class="card-body">
            <form method="POST" action="{{ route('receta.agregar', $itemMenu->id_item_menu) }}" class="row g-3">
                @csrf
                <div class="col-md-5">
                    <label class="form-label">Ingrediente</label>
                    <select name="id_ingrediente" class="form-select" required>
                        <option value="">Seleccione...</option>
                        @foreach($ingredientesDisponibles as $ing)
                            <option value="{{ $ing->id_ingrediente }}">{{ $ing->nombre }} ({{ $ing->unidad_medida }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cantidad necesaria (para 1 unidad)</label>
                    <input type="number" step="0.001" min="0.001" name="cantidad_necesaria" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unidad</label>
                    <select name="unidad_receta" class="form-select" required>
                        @foreach(['gramos','kilogramos','mililitros','litros','unidades','porciones'] as $u)
                            <option value="{{ $u }}">{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Observaciones</label>
                    <input type="text" name="observaciones" class="form-control" placeholder="Opcional">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Agregar</button>
                    <a href="{{ url('/admin/item-menu') }}" class="btn btn-secondary">Volver</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Ingredientes de la receta (para 1 unidad)</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Ingrediente</th>
                            <th style="width:160px">Cantidad</th>
                            <th style="width:160px">Unidad</th>
                            <th style="width:220px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($itemMenu->recetas as $ingrediente)
                            <tr>
                                <td>{{ $ingrediente->nombre ?? '[ELIMINADO]' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('receta.actualizar', [$itemMenu->id_item_menu, $ingrediente->id_ingrediente]) }}" class="d-flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" step="0.001" min="0.001" name="cantidad_necesaria" value="{{ $ingrediente->pivot->cantidad_necesaria }}" class="form-control" required>
                                </td>
                                <td>
                                        <select name="unidad_receta" class="form-select" required>
                                            @php($unidadActual = $ingrediente->pivot->unidad_receta)
                                            @foreach(['gramos','kilogramos','mililitros','litros','unidades','porciones'] as $u)
                                                <option value="{{ $u }}" @selected($u==$unidadActual)>{{ $u }}</option>
                                            @endforeach
                                        </select>
                                </td>
                                <td>
                                        <button type="submit" class="btn btn-sm btn-success mr-2"><i class="fas fa-save"></i> Guardar</button>
                                    </form>
                                    <form method="POST" action="{{ route('receta.eliminar', [$itemMenu->id_item_menu, $ingrediente->id_ingrediente]) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar ingrediente de la receta?')"><i class="fas fa-trash"></i> Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">Sin ingredientes definidos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
