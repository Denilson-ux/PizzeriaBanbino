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
            <form method="POST" action="{{ route('receta.agregar', $itemMenu->id_item_menu) }}" class="row g-3" id="form-agregar-ingrediente">
                @csrf
                <div class="col-md-5">
                    <label class="form-label">Ingrediente</label>
                    <select name="id_ingrediente" id="id_ingrediente" class="form-select" required>
                        <option value="">Seleccione...</option>
                        @foreach($ingredientesDisponibles as $ing)
                            <option value="{{ $ing->id_ingrediente }}" data-unidad="{{ $ing->unidad_medida }}">{{ $ing->nombre }} ({{ $ing->unidad_medida }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cantidad necesaria (para 1 unidad)</label>
                    <input type="number" step="0.001" min="0.001" name="cantidad_necesaria" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unidad</label>
                    <select name="unidad_receta" id="unidad_receta" class="form-select" required>
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
                    <button type="submit" class="btn btn-primary btn-sm" title="Agregar" data-toggle="tooltip"><i class="fas fa-plus"></i></button>
                    <a href="{{ url('/admin/item-menu') }}" class="btn btn-secondary btn-sm" title="Volver" data-toggle="tooltip"><i class="fas fa-arrow-left"></i></a>
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
                            <th style="width:160px">Acciones</th>
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
                                <td class="text-nowrap">
                                        <button type="submit" class="btn btn-sm btn-success mr-1" title="Guardar" data-toggle="tooltip"><i class="fas fa-save"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('receta.eliminar', [$itemMenu->id_item_menu, $ingrediente->id_ingrediente]) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" data-toggle="tooltip" onclick="return confirm('¿Eliminar ingrediente de la receta?')"><i class="fas fa-trash"></i></button>
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

@push('js')
<script>
// Al seleccionar un ingrediente, se auto-selecciona su unidad de medida
(function(){
  const selIngrediente = document.getElementById('id_ingrediente');
  const selUnidad = document.getElementById('unidad_receta');
  if(selIngrediente && selUnidad){
    selIngrediente.addEventListener('change', function(){
      const opt = selIngrediente.options[selIngrediente.selectedIndex];
      const unidad = opt ? opt.getAttribute('data-unidad') : '';
      if(unidad){
        for (const o of selUnidad.options){
          o.selected = (o.value === unidad);
        }
      }
    });
  }
})();
</script>
@endpush
