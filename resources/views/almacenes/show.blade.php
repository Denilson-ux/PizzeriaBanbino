@extends('adminlte::page')

@section('title', 'Detalles del Almacén')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $almacen->nombre }}</h1>
        <div>
            <a href="{{ route('almacenes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
            <a href="{{ route('almacenes.edit', $almacen->id_almacen) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-warehouse"></i> Información del Almacén</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Nombre</dt><dd class="col-7">{{ $almacen->nombre }}</dd>
                    <dt class="col-5 text-muted">Estado</dt><dd class="col-7">
                        <span class="badge badge-{{ $almacen->estado=='activo'?'success':'danger' }}">{{ ucfirst($almacen->estado) }}</span>
                    </dd>
                    @if($almacen->descripcion)
                        <dt class="col-5 text-muted">Descripción</dt><dd class="col-7">{{ $almacen->descripcion }}</dd>
                    @endif
                    @if($almacen->ubicacion)
                        <dt class="col-5 text-muted">Ubicación</dt><dd class="col-7"><i class="fas fa-map-marker-alt text-danger"></i> {{ $almacen->ubicacion }}</dd>
                    @endif
                    @if($almacen->responsable)
                        <dt class="col-5 text-muted">Responsable</dt><dd class="col-7"><i class="fas fa-user text-primary"></i> {{ $almacen->responsable }}</dd>
                    @endif
                    @if($almacen->telefono)
                        <dt class="col-5 text-muted">Teléfono</dt><dd class="col-7"><i class="fas fa-phone text-success"></i> {{ $almacen->telefono }}</dd>
                    @endif
                    <dt class="col-5 text-muted">Creado</dt><dd class="col-7"><small>{{ $almacen->created_at->format('d/m/Y H:i') }}</small></dd>
                    <dt class="col-5 text-muted">Actualizado</dt><dd class="col-7"><small>{{ $almacen->updated_at->format('d/m/Y H:i') }}</small></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-3"><x-adminlte-small-box title="{{ $estadisticas['total_ingredientes'] }}" text="Ingredientes" icon="fas fa-boxes" theme="primary"/></div>
            <div class="col-md-3"><x-adminlte-small-box title="{{ $estadisticas['productos_con_stock'] }}" text="Con stock" icon="fas fa-check-circle" theme="success"/></div>
            <div class="col-md-3"><x-adminlte-small-box title="{{ $estadisticas['productos_stock_bajo'] }}" text="Stock bajo" icon="fas fa-exclamation-triangle" theme="warning"/></div>
            <div class="col-md-3"><x-adminlte-small-box title="{{ formatCurrency($estadisticas['valor_total_inventario'],2) }}" text="Valor total" icon="fas fa-dollar-sign" theme="info"/></div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><i class="fas fa-list"></i> Inventario del Almacén</div>
            <div class="card-body">
                @if($inventario->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Ingrediente</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Mín/Máx</th>
                                <th>Unidad</th>
                                <th>Costo</th>
                                <th>Valor</th>
                                <th>Estado</th>
                                <th>Ubicación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventario as $item)
                            <tr class="{{ $item->stock_bajo ? 'table-warning' : '' }}">
                                <td><strong>{{ $item->ingrediente->nombre }}</strong></td>
                                <td><span class="badge badge-secondary">{{ $item->ingrediente->categoria ?? 'Sin categoría' }}</span></td>
                                <td><span class="font-weight-bold {{ $item->stock_actual <= 0 ? 'text-danger' : ($item->stock_bajo ? 'text-warning' : 'text-success') }}">{{ number_format($item->stock_actual,2) }}</span></td>
                                <td><small>Min: {{ number_format($item->stock_minimo,2) }}<br>Máx: {{ number_format($item->stock_maximo,2) }}</small></td>
                                <td>{{ $item->unidad_medida }}</td>
                                <td>{{ formatCurrency($item->costo_unitario_promedio,2) }}</td>
                                <td>{{ formatCurrency($item->valor_total_stock,2) }}</td>
                                <td><span class="badge badge-{{ $item->estado=='activo'?'success':'danger' }}">{{ ucfirst($item->estado) }}</span></td>
                                <td>{{ $item->ubicacion_fisica ?? 'No especificada' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($inventario->hasPages())
                <div class="d-flex justify-content-center mt-3">{{ $inventario->links() }}</div>
                @endif
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <p>Este almacén no tiene inventario registrado.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card border-info">
            <div class="card-header bg-light"><i class="fas fa-shopping-cart"></i> Compras recientes</div>
            <div class="card-body">
                @php($compras = $almacen->compras()->with('proveedor')->latest()->take(5)->get())
                @if($compras->count())
                    <div class="list-group list-group-flush">
                        @foreach($compras as $compra)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div><strong>{{ $compra->numero_compra }}</strong><br><small class="text-muted">{{ $compra->proveedor->nombre ?? 'Sin proveedor' }}</small></div>
                            <div class="text-right"><span class="text-success font-weight-bold">{{ formatCurrency($compra->total,2) }}</span><br><small class="text-muted">{{ $compra->fecha_compra->format('d/m/Y') }}</small></div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No hay compras registradas para este almacén.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-warning">
            <div class="card-header bg-light"><i class="fas fa-info-circle"></i> Información</div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Las compras destinadas a este almacén actualizan automáticamente el inventario.</li>
                    <li>El stock se reduce al preparar recetas.</li>
                    <li>Los costos se calculan por promedio ponderado.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection