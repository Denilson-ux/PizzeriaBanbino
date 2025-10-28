@extends('adminlte::page')

@section('title', 'Nueva Compra de Ingredientes')

@section('content_header')
    <h1>Nueva Compra de Ingredientes</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('compras.store') }}" id="compraForm">
                @csrf
                <div class="row">
                    <!-- Información general -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle"></i> Información General
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
                                    <label for="id_proveedor">Proveedor *</label>
                                    <select class="form-control select2" id="id_proveedor" name="id_proveedor" required>
                                        <option value="">Seleccionar proveedor...</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id_proveedor }}" {{ old('id_proveedor') == $proveedor->id_proveedor ? 'selected' : '' }}>
                                                {{ $proveedor->nombre }} - {{ $proveedor->ruc }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_almacen_destino">Almacén Destino *</label>
                                    <select class="form-control select2" id="id_almacen_destino" name="id_almacen_destino" required>
                                        <option value="">Seleccionar almacén...</option>
                                        @foreach($almacenes as $almacen)
                                            <option value="{{ $almacen->id_almacen }}" 
                                                    {{ old('id_almacen_destino') == $almacen->id_almacen ? 'selected' : '' }}
                                                    data-ubicacion="{{ $almacen->ubicacion }}"
                                                    data-responsable="{{ $almacen->responsable }}">
                                                {{ $almacen->nombre }}
                                                @if($almacen->ubicacion)
                                                    - {{ $almacen->ubicacion }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted" id="infoAlmacen" style="display:none;">
                                        <i class="fas fa-map-marker-alt"></i> <span id="ubicacionAlmacen"></span><br>
                                        <i class="fas fa-user"></i> Responsable: <span id="responsableAlmacen"></span>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="fecha_compra">Fecha de Compra *</label>
                                    <input type="date" class="form-control" id="fecha_compra" name="fecha_compra" value="{{ old('fecha_compra', date('Y-m-d')) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="fecha_entrega">Fecha de Entrega</label>
                                    <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" value="{{ old('fecha_entrega') }}">
                                </div>

                                <div class="form-group">
                                    <label for="tipo_compra">Tipo de Compra *</label>
                                    <select class="form-control" id="tipo_compra" name="tipo_compra" required>
                                        <option value="contado" {{ old('tipo_compra', 'contado') == 'contado' ? 'selected' : '' }}>Al Contado</option>
                                        <option value="credito" {{ old('tipo_compra') == 'credito' ? 'selected' : '' }}>A Crédito</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="numero_factura">Número de Factura</label>
                                    <input type="text" class="form-control" id="numero_factura" name="numero_factura" value="{{ old('numero_factura') }}" placeholder="F001-00001234">
                                </div>

                                <div class="form-group">
                                    <label for="observaciones">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Panel de información del almacén seleccionado -->
                        <div class="card" id="panelAlmacen" style="display:none;">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-warehouse"></i> Información del Almacén
                                </h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Nombre:</strong> <span id="nombreAlmacenInfo"></span></p>
                                <p><strong>Ubicación:</strong> <span id="ubicacionAlmacenInfo"></span></p>
                                <p><strong>Responsable:</strong> <span id="responsableAlmacenInfo"></span></p>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Los ingredientes comprados se almacenarán en este almacén cuando se complete la compra.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ingredientes -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-shopping-cart"></i> Ingredientes de la Compra
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-success" id="agregarIngrediente">
                                        <i class="fas fa-plus"></i> Agregar Ingrediente
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="ingredientesTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="25%">Ingrediente</th>
                                                <th width="15%">Cantidad</th>
                                                <th width="10%">Unidad</th>
                                                <th width="15%">Precio Unit.</th>
                                                <th width="15%">Subtotal</th>
                                                <th width="15%">Stock Actual</th>
                                                <th width="5%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ingredientesBody">
                                            <!-- Los ingredientes se agregarán dinámicamente -->
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-light">
                                                <td colspan="4"><strong>Subtotal:</strong></td>
                                                <td><strong>Bs/. <span id="subtotalGeneral">0.00</span></strong></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td colspan="4"><strong>IGV (18%):</strong></td>
                                                <td><strong>Bs/. <span id="igvGeneral">0.00</span></strong></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr class="bg-primary text-white">
                                                <td colspan="4"><strong>TOTAL:</strong></td>
                                                <td><strong>Bs/. <span id="totalGeneral">0.00</span></strong></td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="alert alert-info mt-3" id="infoCompra" style="display: none;">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Información:</strong> Esta compra se creará en estado "Pendiente". 
                                    Los ingredientes se agregaran al inventario del almacén 
                                    <strong><span id="nombreAlmacenCompra"></span></strong> 
                                    cuando se complete la compra.
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary" id="guardarCompra" disabled>
                                            <i class="fas fa-save"></i> Guardar Compra
                                        </button>
                                        <a href="{{ route('compras.index') }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="text-muted">Total de ingredientes: <span id="totalIngredientes">0</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar ingrediente -->
<div class="modal fade" id="ingredienteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar Ingrediente a la Compra</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="ingredienteForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ingrediente_select">Ingrediente *</label>
                                <select class="form-control select2-modal" id="ingrediente_select" required>
                                    <option value="">Seleccionar ingrediente...</option>
                                    @php
                                        $categorias = $ingredientes->groupBy('categoria');
                                    @endphp
                                    @foreach($categorias as $categoria => $ingredientesCategoria)
                                        <optgroup label="{{ ucfirst($categoria ?? 'Otros') }}">
                                            @foreach($ingredientesCategoria as $ingrediente)
                                                <option value="{{ $ingrediente->id_ingrediente }}" 
                                                        data-nombre="{{ $ingrediente->nombre }}" 
                                                        data-categoria="{{ $ingrediente->categoria ?? 'Otros' }}"
                                                        data-stock="{{ $ingrediente->stock_actual }}"
                                                        data-unidad="{{ $ingrediente->unidad_medida }}"
                                                        data-stock-bajo="{{ $ingrediente->stock_bajo ? 1 : 0 }}">
                                                    {{ $ingrediente->nombre }} ({{ $ingrediente->unidad_medida }})
                                                    @if($ingrediente->stock_bajo)
                                                        <span class="text-danger">- Stock Bajo</span>
                                                    @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cantidad">Cantidad *</label>
                                <input type="number" class="form-control" id="cantidad" min="0.01" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="precio_unitario">Precio Unitario *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Bs/.</span>
                                    </div>
                                    <input type="number" class="form-control" id="precio_unitario" step="0.01" min="0" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="alert alert-info" id="stockInfo" style="display: none;">
                                <small>
                                    <strong>Stock actual:</strong> <span id="stockActual">0</span> <span id="unidadMedida">unidades</span>
                                    <br><span id="estadoStock"></span>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-secondary" id="unidadInfo" style="display: none;">
                                <small><strong>Unidad:</strong> <span id="unidadIngrediente">-</span></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-success" id="subtotalInfo" style="display: none;">
                                <strong>Subtotal: Bs/. <span id="subtotalIngrediente">0.00</span></strong>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="observaciones_ingrediente">Observaciones del Ingrediente</label>
                        <textarea class="form-control" id="observaciones_ingrediente" rows="2" placeholder="Lote, fecha vencimiento, marca, etc."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="agregarIngredienteBtn">Agregar Ingrediente</button>
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
    let ingredientesAgregados = [];
    let contadorIngredientes = 0;

    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Seleccionar...',
            allowClear: true
        });
        
        $('.select2-modal').select2({
            theme: 'bootstrap4',
            placeholder: 'Seleccionar ingrediente...',
            dropdownParent: $('#ingredienteModal')
        });

        // Mostrar información del almacén seleccionado
        $('#id_almacen_destino').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const valor = $(this).val();
            
            if (valor) {
                const nombre = selectedOption.text().split(' - ')[0];
                const ubicacion = selectedOption.data('ubicacion') || 'No especificada';
                const responsable = selectedOption.data('responsable') || 'No asignado';
                
                // Actualizar información del almacén
                $('#nombreAlmacenInfo').text(nombre);
                $('#ubicacionAlmacenInfo').text(ubicacion);
                $('#responsableAlmacenInfo').text(responsable);
                $('#panelAlmacen').show();
                
                // Actualizar información en el mensaje de compra
                $('#nombreAlmacenCompra').text(nombre);
                
                // Mostrar información del almacén en el select
                let infoText = '';
                if (ubicacion && ubicacion !== 'No especificada') {
                    infoText += ubicacion;
                }
                if (responsable && responsable !== 'No asignado') {
                    if (infoText) infoText += '\n';
                    infoText += 'Responsable: ' + responsable;
                }
                
                if (infoText) {
                    $('#ubicacionAlmacen').text(ubicacion);
                    $('#responsableAlmacen').text(responsable);
                    $('#infoAlmacen').show();
                }
                
                // Actualizar el mensaje de información de compra si hay ingredientes
                if (ingredientesAgregados.length > 0) {
                    $('#infoCompra').show();
                }
            } else {
                $('#panelAlmacen').hide();
                $('#infoAlmacen').hide();
                $('#infoCompra').hide();
            }
        });

        // Agregar ingrediente al modal
        $('#agregarIngrediente').click(function() {
            // Verificar que se haya seleccionado un almacén
            if (!$('#id_almacen_destino').val()) {
                alert('Primero debes seleccionar un almacén de destino.');
                $('#id_almacen_destino').focus();
                return;
            }
            
            $('#ingredienteForm')[0].reset();
            $('#stockInfo, #subtotalInfo, #unidadInfo').hide();
            $('#ingrediente_select').val(null).trigger('change');
            $('#ingredienteModal').modal('show');
        });

        // Mostrar información al seleccionar ingrediente
        $('#ingrediente_select').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const valor = $(this).val();
            
            if (valor) {
                const stock = selectedOption.data('stock');
                const unidad = selectedOption.data('unidad');
                const stockBajo = selectedOption.data('stock-bajo');
                
                $('#stockActual').text(stock);
                $('#unidadMedida').text(unidad);
                $('#unidadIngrediente').text(unidad);
                
                if (stockBajo) {
                    $('#estadoStock').html('<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Stock Bajo</span>');
                } else {
                    $('#estadoStock').html('<span class="text-success"><i class="fas fa-check"></i> Stock Normal</span>');
                }
                
                $('#stockInfo, #unidadInfo').show();
                
                // NO autocompletar precio - mantener vacío para entrada manual
                $('#precio_unitario').focus();
            } else {
                $('#stockInfo, #subtotalInfo, #unidadInfo').hide();
            }
        });

        // Calcular subtotal del ingrediente en tiempo real
        $('#cantidad, #precio_unitario').on('input', calcularSubtotalIngrediente);

        function calcularSubtotalIngrediente() {
            const cantidad = parseFloat($('#cantidad').val()) || 0;
            const precio = parseFloat($('#precio_unitario').val()) || 0;
            const subtotal = cantidad * precio;
            
            $('#subtotalIngrediente').text(subtotal.toFixed(2));
            
            if (subtotal > 0) {
                $('#subtotalInfo').show();
            } else {
                $('#subtotalInfo').hide();
            }
        }

        // Agregar ingrediente a la tabla
        $('#agregarIngredienteBtn').click(function() {
            const selectedOption = $('#ingrediente_select option:selected');
            const ingredienteId = $('#ingrediente_select').val();
            const ingredienteNombre = selectedOption.data('nombre');
            const ingredienteCategoria = selectedOption.data('categoria');
            const cantidad = parseFloat($('#cantidad').val());
            const precio = parseFloat($('#precio_unitario').val());
            const observaciones = $('#observaciones_ingrediente').val();
            const stockActual = selectedOption.data('stock');
            const unidadMedida = selectedOption.data('unidad');
            
            if (!ingredienteId || !cantidad || !precio) {
                alert('Por favor completa todos los campos obligatorios.');
                return;
            }
            
            // Verificar si ya existe
            if (ingredientesAgregados.find(i => i.id == ingredienteId)) {
                alert('Este ingrediente ya fue agregado. Edita la cantidad existente.');
                return;
            }
            
            const subtotal = cantidad * precio;
            contadorIngredientes++;
            
            const ingrediente = {
                id: ingredienteId,
                nombre: ingredienteNombre,
                categoria: ingredienteCategoria,
                cantidad: cantidad,
                precio: precio,
                subtotal: subtotal,
                observaciones: observaciones,
                stock: stockActual,
                unidad: unidadMedida,
                contador: contadorIngredientes
            };
            
            ingredientesAgregados.push(ingrediente);
            agregarFilaIngrediente(ingrediente);
            actualizarTotales();
            
            $('#ingredienteModal').modal('hide');
        });

        function agregarFilaIngrediente(ingrediente) {
            const fila = `
                <tr id="ingrediente_${ingrediente.contador}">
                    <td>
                        <strong>${ingrediente.nombre}</strong>
                        <br><small class="text-muted">${ingrediente.categoria}</small>
                        <input type="hidden" name="ingredientes[${ingrediente.contador}][id_ingrediente]" value="${ingrediente.id}">
                        <input type="hidden" name="ingredientes[${ingrediente.contador}][observaciones]" value="${ingrediente.observaciones}">
                    </td>
                    <td>
                        <input type="number" class="form-control cantidad-input" 
                               name="ingredientes[${ingrediente.contador}][cantidad]" 
                               value="${ingrediente.cantidad}" min="0.01" step="0.01"
                               data-contador="${ingrediente.contador}" required>
                    </td>
                    <td>
                        <small class="badge badge-info">${ingrediente.unidad}</small>
                    </td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">S/.</span>
                            </div>
                            <input type="number" class="form-control precio-input" 
                                   name="ingredientes[${ingrediente.contador}][precio_unitario]" 
                                   value="${ingrediente.precio}" step="0.01" min="0" 
                                   data-contador="${ingrediente.contador}" required>
                        </div>
                    </td>
                    <td>
                        <strong class="subtotal-ingrediente">S/. ${ingrediente.subtotal.toFixed(2)}</strong>
                    </td>
                    <td>
                        <small class="badge badge-secondary">${ingrediente.stock} ${ingrediente.unidad}</small>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarIngrediente(${ingrediente.contador})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#ingredientesBody').append(fila);
            $('#infoCompra').show();
            $('#guardarCompra').prop('disabled', false);
        }

        // Actualizar cálculos cuando cambien cantidad o precio
        $(document).on('input', '.cantidad-input, .precio-input', function() {
            const contador = $(this).data('contador');
            const fila = $(`#ingrediente_${contador}`);
            const cantidad = parseFloat(fila.find('.cantidad-input').val()) || 0;
            const precio = parseFloat(fila.find('.precio-input').val()) || 0;
            const subtotal = cantidad * precio;
            
            fila.find('.subtotal-ingrediente').text('S/. ' + subtotal.toFixed(2));
            
            // Actualizar en el array
            const ingredienteIndex = ingredientesAgregados.findIndex(i => i.contador === contador);
            if (ingredienteIndex !== -1) {
                ingredientesAgregados[ingredienteIndex].cantidad = cantidad;
                ingredientesAgregados[ingredienteIndex].precio = precio;
                ingredientesAgregados[ingredienteIndex].subtotal = subtotal;
            }
            
            actualizarTotales();
        });

        function actualizarTotales() {
            let subtotal = 0;
            ingredientesAgregados.forEach(i => {
                subtotal += i.subtotal;
            });
            
            const igv = subtotal * 0.18;
            const total = subtotal + igv;
            
            $('#subtotalGeneral').text(subtotal.toFixed(2));
            $('#igvGeneral').text(igv.toFixed(2));
            $('#totalGeneral').text(total.toFixed(2));
            $('#totalIngredientes').text(ingredientesAgregados.length);
        }

        // Validación antes de enviar
        $('#compraForm').on('submit', function(e) {
            if (!$('#id_almacen_destino').val()) {
                e.preventDefault();
                alert('Debes seleccionar un almacén de destino.');
                $('#id_almacen_destino').focus();
                return false;
            }
            
            if (ingredientesAgregados.length === 0) {
                e.preventDefault();
                alert('Debes agregar al menos un ingrediente a la compra.');
                return false;
            }
        });
    });

    // Función global para eliminar (llamada desde HTML)
    window.eliminarIngrediente = function(contador) {
        if (confirm('¿Estás seguro de eliminar este ingrediente?')) {
            $(`#ingrediente_${contador}`).remove();
            ingredientesAgregados = ingredientesAgregados.filter(i => i.contador !== contador);
            actualizarTotales();
            
            if (ingredientesAgregados.length === 0) {
                $('#guardarCompra').prop('disabled', true);
                $('#infoCompra').hide();
            }
        }
    }
</script>
@endsection