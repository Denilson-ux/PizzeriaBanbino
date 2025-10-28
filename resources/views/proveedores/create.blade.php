@extends('adminlte::page')

@section('title', 'Nuevo Proveedor')

@section('content_header')
    <h1>Nuevo Proveedor</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> Registrar Nuevo Proveedor
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <form method="POST" action="{{ route('proveedores.store') }}">
                    @csrf
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

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nombre">Nombre del Proveedor *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                    <small class="form-text text-muted">Razón social o nombre comercial</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ruc">RUC *</label>
                                    <input type="text" class="form-control" id="ruc" name="ruc" value="{{ old('ruc') }}" maxlength="11" required>
                                    <small class="form-text text-muted">11 dígitos</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono') }}">
                                    <small class="form-text text-muted">Número de contacto principal</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                                    <small class="form-text text-muted">Para envío de órdenes de compra</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contacto">Persona de Contacto</label>
                            <input type="text" class="form-control" id="contacto" name="contacto" value="{{ old('contacto') }}">
                            <small class="form-text text-muted">Nombre del representante o vendedor</small>
                        </div>

                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="3">{{ old('direccion') }}</textarea>
                            <small class="form-text text-muted">Dirección completa del proveedor</small>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado *</label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="activo" {{ old('estado', 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <small class="form-text text-muted">Los proveedores inactivos no aparecerán en las compras</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información:</strong> Una vez creado el proveedor, podrás realizar compras de ingredientes y productos que se agregarán automáticamente al almacén.
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Proveedor
                                </button>
                                <a href="{{ route('proveedores.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
</script>
@endsection