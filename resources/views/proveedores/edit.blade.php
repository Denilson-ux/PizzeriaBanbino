@extends('adminlte::page')

@section('title', 'Editar Proveedor')

@section('content_header')
    <h1>Editar Proveedor: {{ $proveedor->nombre }}</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Modificar Información del Proveedor
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('proveedores.show', $proveedor->id_proveedor) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </a>
                            <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('proveedores.update', $proveedor->id_proveedor) }}">
                    @csrf
                    @method('PUT')
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

                        <!-- Información actual -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Fecha de registro:</strong> {{ $proveedor->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Estado actual:</strong> 
                                    <span class="badge badge-{{ $proveedor->estado == 'activo' ? 'success' : 'danger' }}">
                                        {{ ucfirst($proveedor->estado) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nombre">Nombre del Proveedor *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required>
                                    <small class="form-text text-muted">Razón social o nombre comercial</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ruc">RUC *</label>
                                    <input type="text" class="form-control" id="ruc" name="ruc" value="{{ old('ruc', $proveedor->ruc) }}" maxlength="11" required>
                                    <small class="form-text text-muted">11 dígitos</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}">
                                    <small class="form-text text-muted">Número de contacto principal</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $proveedor->email) }}">
                                    <small class="form-text text-muted">Para envío de órdenes de compra</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contacto">Persona de Contacto</label>
                            <input type="text" class="form-control" id="contacto" name="contacto" value="{{ old('contacto', $proveedor->contacto) }}">
                            <small class="form-text text-muted">Nombre del representante o vendedor</small>
                        </div>

                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="3">{{ old('direccion', $proveedor->direccion) }}</textarea>
                            <small class="form-text text-muted">Dirección completa del proveedor</small>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado *</label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="activo" {{ old('estado', $proveedor->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado', $proveedor->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <small class="form-text text-muted">Los proveedores inactivos no aparecerán en las nuevas compras</small>
                        </div>

                        @if($proveedor->compras()->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Importante:</strong> Este proveedor tiene {{ $proveedor->compras()->count() }} compras registradas. 
                                Si lo desactivas, no aparecerá para nuevas compras, pero las compras existentes se mantendrán.
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <a href="{{ route('proveedores.show', $proveedor->id_proveedor) }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                @if($proveedor->compras()->count() == 0)
                                    <button type="button" class="btn btn-danger" onclick="eliminarProveedor()">
                                        <i class="fas fa-trash"></i> Eliminar Proveedor
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            @if($proveedor->compras()->count() > 0)
                <!-- Resumen de compras -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Resumen de Compras
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-shopping-basket"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Compras</span>
                                        <span class="info-box-number">{{ $proveedor->compras()->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Completadas</span>
                                        <span class="info-box-number">{{ $proveedor->compras()->completadas()->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pendientes</span>
                                        <span class="info-box-number">{{ $proveedor->compras()->pendientes()->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary">
                                        <i class="fas fa-dollar-sign"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Monto Total</span>
                                        <span class="info-box-number">S/. {{ number_format($proveedor->compras()->completadas()->sum('total'), 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Validación de RUC en tiempo real
        $('#ruc').on('input', function() {
            let ruc = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(ruc);
            
            if (ruc.length === 11) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else if (ruc.length > 0) {
                $(this).removeClass('is-valid').addClass('is-invalid');
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        });
        
        // Validación de email
        $('#email').on('blur', function() {
            const email = $(this).val();
            if (email && !isValidEmail(email)) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Ingresa un email válido</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        
        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    });

    function eliminarProveedor() {
        if (confirm('¿Estás seguro de eliminar permanentemente el proveedor "{{ $proveedor->nombre }}"? Esta acción no se puede deshacer.')) {
            // Crear formulario temporal para DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("proveedores.destroy", $proveedor->id_proveedor) }}';
            
            // Token CSRF
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = $('meta[name="csrf-token"]').attr('content');
            form.appendChild(csrfField);
            
            // Método DELETE
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection