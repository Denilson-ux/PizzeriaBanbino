<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Reporte de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .card-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #c82333, #a71e2a);
        }
        .form-label {
            font-weight: 600;
        }
        .card {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border: none;
        }
        .alert {
            border: none;
            border-radius: 10px;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .preview-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
        }
        .preview-btn:hover {
            background: linear-gradient(135deg, #20c997, #17a2b8);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h1 class="display-6 text-primary">
                        <i class="fas fa-pizza-slice"></i> Pizzería Bambino
                    </h1>
                    <p class="lead">Sistema de Reportes por Correo Electrónico</p>
                </div>

                <!-- Mensajes de éxito o error -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Formulario principal -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-envelope"></i> Enviar Reporte de Pedidos
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('email.enviar-reporte') }}" id="reporteForm">
                            @csrf
                            
                            <!-- Destinatarios -->
                            <div class="mb-3">
                                <label for="destinatarios" class="form-label">
                                    <i class="fas fa-users me-1"></i> Destinatarios *
                                </label>
                                <input type="text" 
                                       class="form-control @error('destinatarios') is-invalid @enderror" 
                                       id="destinatarios" 
                                       name="destinatarios" 
                                       value="{{ old('destinatarios') }}"
                                       placeholder="ejemplo1@gmail.com, ejemplo2@gmail.com"
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> 
                                    Introduzca los emails separados por coma. Ejemplo: juan@ejemplo.com, maria@ejemplo.com
                                </div>
                                @error('destinatarios')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fechas -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_inicio" class="form-label">
                                            <i class="fas fa-calendar-alt me-1"></i> Fecha Inicial *
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                               id="fecha_inicio" 
                                               name="fecha_inicio" 
                                               value="{{ old('fecha_inicio', date('Y-m-01')) }}"
                                               required>
                                        @error('fecha_inicio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_fin" class="form-label">
                                            <i class="fas fa-calendar-alt me-1"></i> Fecha Final *
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('fecha_fin') is-invalid @enderror" 
                                               id="fecha_fin" 
                                               name="fecha_fin" 
                                               value="{{ old('fecha_fin', date('Y-m-d')) }}"
                                               required>
                                        @error('fecha_fin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Cliente -->
                            <div class="mb-3">
                                <label for="cliente_id" class="form-label">
                                    <i class="fas fa-user me-1"></i> Filtrar por Cliente (Opcional)
                                </label>
                                <select class="form-select @error('cliente_id') is-invalid @enderror" 
                                        id="cliente_id" 
                                        name="cliente_id">
                                    <option value="">Todos los clientes</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente['id'] }}" 
                                                {{ old('cliente_id') == $cliente['id'] ? 'selected' : '' }}>
                                            {{ $cliente['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> 
                                    Seleccione un cliente específico o deje vacío para incluir todos los pedidos
                                </div>
                                @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Asunto personalizado -->
                            <div class="mb-4">
                                <label for="asunto" class="form-label">
                                    <i class="fas fa-tag me-1"></i> Asunto Personalizado (Opcional)
                                </label>
                                <input type="text" 
                                       class="form-control @error('asunto') is-invalid @enderror" 
                                       id="asunto" 
                                       name="asunto" 
                                       value="{{ old('asunto') }}"
                                       placeholder="Se generará automáticamente si se deja vacío">
                                @error('asunto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Botones -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-info preview-btn me-md-2" id="previewBtn">
                                    <i class="fas fa-eye"></i> Vista Previa
                                </button>
                                <button type="button" class="btn btn-secondary me-md-2" id="testBtn">
                                    <i class="fas fa-cog"></i> Test Conexión
                                </button>
                                <button type="submit" class="btn btn-primary" id="enviarBtn">
                                    <i class="fas fa-paper-plane"></i> Enviar Correo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="mt-4 text-center">
                    <p class="text-muted">
                        <i class="fas fa-server"></i> 
                        Servidor de correo configurado: <strong>{{ config('mail.mailers.smtp.host') }}:{{ config('mail.mailers.smtp.port') }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para vista previa -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye"></i> Vista Previa del Reporte
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="previewContent">
                    <!-- Contenido del preview se cargará aquí -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Botón de vista previa
            document.getElementById('previewBtn').addEventListener('click', function() {
                const form = document.getElementById('reporteForm');
                const formData = new FormData(form);
                
                // Mostrar loading
                document.getElementById('previewContent').innerHTML = 
                    '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2">Generando vista previa...</p></div>';
                
                // Abrir modal
                new bootstrap.Modal(document.getElementById('previewModal')).show();
                
                // Hacer petición AJAX
                fetch('{{ route("email.preview-reporte") }}?' + new URLSearchParams(formData), {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('previewContent').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('previewContent').innerHTML = 
                        '<div class="alert alert-danger m-3"><i class="fas fa-exclamation-triangle"></i> Error al cargar la vista previa: ' + error.message + '</div>';
                });
            });

            // Botón de test de conexión
            document.getElementById('testBtn').addEventListener('click', function() {
                const btn = this;
                const originalText = btn.innerHTML;
                
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Probando...';
                btn.disabled = true;
                
                fetch('{{ route("email.test-conexion") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Conexión exitosa!\n\nHost: ' + data.config.host + '\nPuerto: ' + data.config.port);
                    } else {
                        alert('❌ Error de conexión:\n\n' + data.message);
                    }
                })
                .catch(error => {
                    alert('❌ Error de conexión:\n\n' + error.message);
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            });

            // Validación de fechas
            document.getElementById('fecha_inicio').addEventListener('change', function() {
                const fechaInicio = new Date(this.value);
                const fechaFin = document.getElementById('fecha_fin');
                
                if (fechaFin.value && new Date(fechaFin.value) < fechaInicio) {
                    fechaFin.value = this.value;
                }
                
                fechaFin.min = this.value;
            });
        });
    </script>
</body>
</html>