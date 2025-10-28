@extends('adminlte::page')

@section('title', 'Editar Compra')

@section('content_header')
    <h1>Editar Compra: {{ $compra->numero_compra }}</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @if($compra->estado !== 'pendiente')
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Atención:</strong> Solo se pueden editar compras en estado pendiente.
                    <a href="{{ route('compras.show', $compra->id_compra) }}" class="btn btn-sm btn-info ml-2">
                        <i class="fas fa-eye"></i> Ver Detalle
                    </a>
                </div>
            @else
            <form method="POST" action="{{ route('compras.update', $compra->id_compra) }}" id="compraForm">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Información general -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-edit"></i> Editar Información
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

                                <div class="alert alert-info">
                                    <strong>Número:</strong> {{ $compra->numero_compra }}
                                    <br><strong>Estado:</strong> {!! $compra->estado_badge !!}
                                </div>

                                <div class="form-group">
                                    <label for="id_proveedor">Proveedor *</label>
                                    <select class="form-control select2" id="id_proveedor" name="id_proveedor" required>
                                        <option value="">Seleccionar proveedor...</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id_proveedor }}" {{ old('id_proveedor', $compra->id_proveedor) == $proveedor->id_proveedor ? 'selected' : '' }}>
                                                {{ $proveedor->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="fecha_compra">Fecha de Compra *</label>
                                    <input type="date" class="form-control" id="fecha_compra" name="fecha_compra" value="{{ old('fecha_compra', $compra->fecha_compra->format('Y-m-d')) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="fecha_entrega">Fecha de Entrega</label>
                                    <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" value="{{ old('fecha_entrega', $compra->fecha_entrega?->format('Y-m-d')) }}">
                                </div>

                                <div class="form-group">
                                    <label for="tipo_compra">Tipo de Compra *</label>
                                    <select class="form-control" id="tipo_compra" name="tipo_compra" required>
                                        <option value="contado" {{ old('tipo_compra', $compra->tipo_compra) == 'contado' ? 'selected' : '' }}>Al Contado</option>
                                        <option value="credito" {{ old('tipo_compra', $compra->tipo_compra) == 'credito' ? 'selected' : '' }}>A Crédito</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="numero_factura">Número de Factura</label>
                                    <input type="text" class="form-control" id="numero_factura" name="numero_factura" value="{{ old('numero_factura', $compra->numero_factura) }}">
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="aplicar_almacen" name="aplicar_almacen" value="1" {{ old('aplicar_almacen', $compra->aplicar_almacen) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="aplicar_almacen">
                                            Aplicar automáticamente al almacén
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="observaciones">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $compra->observaciones) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-shopping-cart"></i> Productos de la Compra
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-success" id="agregarProducto">
                                        <i class="fas fa-plus"></i> Agregar Producto
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="productosTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="30%">Producto</th>
                                                <th width="15%">Cantidad</th>
                                                <th width="20%">Precio Unitario</th>
                                                <th width="20%">Subtotal</th>
                                                <th width="10%">Stock</th>
                                                <th width="5%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productosBody">
                                            @foreach($compra->detalles as $index => $detalle)
                                            <tr id="producto_{{ $index }}">
                                                <td>
                                                    <strong>{{ $detalle->itemMenu->nombre }}</strong>
                                                    <br><small class="text-muted">{{ $detalle->itemMenu->tipoMenu->nombre }}</small>
                                                    <input type="hidden" name="productos[{{ $index }}][id_item_menu]" value="{{ $detalle->id_item_menu }}">
                                                    <input type="hidden" name="productos[{{ $index }}][observaciones]" value="{{ $detalle->observaciones }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control cantidad-input" 
                                                           name="productos[{{ $index }}][cantidad]" 
                                                           value="{{ $detalle->cantidad }}" min="1" 
                                                           data-contador="{{ $index }}" required>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">S/.</span>
                                                        </div>
                                                        <input type="number" class="form-control precio-input" 
                                                               name="productos[{{ $index }}][precio_unitario]" 
                                                               value="{{ $detalle->precio_unitario }}" step="0.01" min="0" 
                                                               data-contador="{{ $index }}" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong class="subtotal-producto">S/. {{ number_format($detalle->subtotal, 2) }}</strong>
                                                </td>
                                                <td>
                                                    @if($detalle->itemMenu->almacen)
                                                        <small class="badge badge-info">{{ $detalle->itemMenu->almacen->stock_actual }}</small>
                                                    @else
                                                        <small class="badge badge-warning">N/A</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto({{ $index }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-light">
                                                <td colspan="3"><strong>Subtotal:</strong></td>
                                                <td><strong>S/. <span id="subtotalGeneral">{{ number_format($compra->subtotal, 2) }}</span></strong></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td colspan="3"><strong>IGV (18%):</strong></td>
                                                <td><strong>S/. <span id="igvGeneral">{{ number_format($compra->impuesto, 2) }}</span></strong></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr class="bg-primary text-white">
                                                <td colspan="3"><strong>TOTAL:</strong></td>
                                                <td><strong>S/. <span id="totalGeneral">{{ number_format($compra->total, 2) }}</span></strong></td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Cambios
                                        </button>
                                        <a href="{{ route('compras.show', $compra->id_compra) }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="text-muted">Productos: <span id="totalProductos">{{ $compra->detalles->count() }}</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

<!-- Modal para agregar producto -->
<div class="modal fade" id="productoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar Producto a la Compra</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="productoForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="producto_select">Producto/Ingrediente *</label>
                                <select class="form-control select2-modal" id="producto_select" required>
                                    <option value="">Seleccionar producto...</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id_item_menu }}" data-nombre="{{ $producto->nombre }}" data-categoria="{{ $producto->tipoMenu->nombre }}">
                                            {{ $producto->nombre }} - {{ $producto->tipoMenu->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cantidad">Cantidad *</label>
                                <input type="number" class="form-control" id="cantidad" min="1" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="precio_unitario">Precio Unitario *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">S/.</span>
                                    </div>
                                    <input type="number" class="form-control" id="precio_unitario" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="observaciones_producto">Observaciones del Producto</label>
                        <textarea class="form-control" id="observaciones_producto" rows="2" placeholder="Lote, fecha vencimiento, etc."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="agregarProductoBtn">Agregar Producto</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let contadorProductos = {{ $compra->detalles->count() }};
    let productosExistentes = [{{ $compra->detalles->pluck('id_item_menu')->implode(',') }}];

    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Seleccionar...',
            allowClear: true
        });
        
        $('.select2-modal').select2({
            theme: 'bootstrap4',
            placeholder: 'Seleccionar producto...',
            dropdownParent: $('#productoModal')
        });

        // Agregar producto al modal
        $('#agregarProducto').click(function() {
            $('#productoForm')[0].reset();
            $('#producto_select').val(null).trigger('change');
            $('#productoModal').modal('show');
        });

        // Agregar producto a la tabla
        $('#agregarProductoBtn').click(function() {
            const productoId = $('#producto_select').val();
            const productoNombre = $('#producto_select option:selected').data('nombre');
            const productoCategoria = $('#producto_select option:selected').data('categoria');
            const cantidad = parseFloat($('#cantidad').val());
            const precio = parseFloat($('#precio_unitario').val());
            const observaciones = $('#observaciones_producto').val();
            
            if (!productoId || !cantidad || !precio) {
                alert('Por favor completa todos los campos obligatorios.');
                return;
            }
            
            // Verificar si ya existe
            if (productosExistentes.includes(parseInt(productoId))) {
                alert('Este producto ya fue agregado. Edita la cantidad existente.');
                return;
            }
            
            const subtotal = cantidad * precio;
            contadorProductos++;
            productosExistentes.push(parseInt(productoId));
            
            const fila = `
                <tr id="producto_${contadorProductos}">
                    <td>
                        <strong>${productoNombre}</strong>
                        <br><small class="text-muted">${productoCategoria}</small>
                        <input type="hidden" name="productos[${contadorProductos}][id_item_menu]" value="${productoId}">
                        <input type="hidden" name="productos[${contadorProductos}][observaciones]" value="${observaciones}">
                    </td>
                    <td>
                        <input type="number" class="form-control cantidad-input" 
                               name="productos[${contadorProductos}][cantidad]" 
                               value="${cantidad}" min="1" 
                               data-contador="${contadorProductos}" required>
                    </td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">S/.</span>
                            </div>
                            <input type="number" class="form-control precio-input" 
                                   name="productos[${contadorProductos}][precio_unitario]" 
                                   value="${precio}" step="0.01" min="0" 
                                   data-contador="${contadorProductos}" required>
                        </div>
                    </td>
                    <td>
                        <strong class="subtotal-producto">S/. ${subtotal.toFixed(2)}</strong>
                    </td>
                    <td>
                        <small class="badge badge-info">0</small>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${contadorProductos})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#productosBody').append(fila);
            actualizarTotales();
            $('#productoModal').modal('hide');
        });

        // Actualizar cálculos cuando cambien cantidad o precio
        $(document).on('input', '.cantidad-input, .precio-input', function() {
            const contador = $(this).data('contador');
            const fila = $(`#producto_${contador}`);
            const cantidad = parseFloat(fila.find('.cantidad-input').val()) || 0;
            const precio = parseFloat(fila.find('.precio-input').val()) || 0;
            const subtotal = cantidad * precio;
            
            fila.find('.subtotal-producto').text('S/. ' + subtotal.toFixed(2));
            actualizarTotales();
        });

        function actualizarTotales() {
            let subtotal = 0;
            
            $('.subtotal-producto').each(function() {
                const valor = parseFloat($(this).text().replace('S/. ', '')) || 0;
                subtotal += valor;
            });
            
            const igv = subtotal * 0.18;
            const total = subtotal + igv;
            
            $('#subtotalGeneral').text(subtotal.toFixed(2));
            $('#igvGeneral').text(igv.toFixed(2));
            $('#totalGeneral').text(total.toFixed(2));
            $('#totalProductos').text($('#productosBody tr').length);
        }
    });

    // Función global para eliminar
    window.eliminarProducto = function(contador) {
        if (confirm('¿Estás seguro de eliminar este producto?')) {
            // Obtener el ID del producto para removerlo de existentes
            const productoId = parseInt($(`#producto_${contador} input[name*="[id_item_menu]"]`).val());
            productosExistentes = productosExistentes.filter(id => id !== productoId);
            
            $(`#producto_${contador}`).remove();
            actualizarTotales();
        }
    }

    function actualizarTotales() {
        let subtotal = 0;
        
        $('.subtotal-producto').each(function() {
            const valor = parseFloat($(this).text().replace('S/. ', '')) || 0;
            subtotal += valor;
        });
        
        const igv = subtotal * 0.18;
        const total = subtotal + igv;
        
        $('#subtotalGeneral').text(subtotal.toFixed(2));
        $('#igvGeneral').text(igv.toFixed(2));
        $('#totalGeneral').text(total.toFixed(2));
        $('#totalProductos').text($('#productosBody tr').length);
    }
</script>
@endsection