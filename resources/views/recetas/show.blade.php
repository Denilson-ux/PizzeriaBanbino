@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Gestionar receta: {{ $itemMenu->nombre }}</h3>
        <a href="{{ url('/admin/item-menu') }}" class="btn btn-secondary">Volver</a>
    </div>

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
                        <option value="gramos">gramos</option>
                        <option value="kilogramos">kilogramos</option>
                        <option value="mililitros">mililitros</option>
                        <option value="litros">litros</option>
                        <option value="unidades">unidades</option>
                        <option value="porciones">porciones</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Observaciones</label>
                    <input type="text" name="observaciones" class="form-control" placeholder="Opcional">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Agregar</button>
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
                            <th style="width:200px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($itemMenu->recetas as $receta)
                            <tr>
                                <td>{{ $receta->ingrediente->nombre }}</td>
                                <td>
                                    <form method="POST" action="{{ route('receta.actualizar', [$itemMenu->id_item_menu, $receta->id_ingrediente]) }}" class="d-flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" step="0.001" min="0.001" name="cantidad_necesaria" value="{{ $receta->pivot->cantidad_necesaria }}" class="form-control" required>
                                </td>
                                <td>
                                        <select name="unidad_receta" class="form-select" required>
                                            @php($unidadActual = $receta->pivot->unidad_receta)
                                            @foreach(['gramos','kilogramos','mililitros','litros','unidades','porciones'] as $u)
                                                <option value="{{ $u }}" @selected($u==$unidadActual)>{{ $u }}</option>
                                            @endforeach
                                        </select>
                                </td>
                                <td>
                                        <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                                    </form>
                                    <form method="POST" action="{{ route('receta.eliminar', [$itemMenu->id_item_menu, $receta->id_ingrediente]) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar ingrediente de la receta?')">Eliminar</button>
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
