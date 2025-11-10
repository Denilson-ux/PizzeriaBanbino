# Sistema de Reportes de Compras por Correo Electrónico

## Descripción

Este sistema permite generar y enviar reportes paramétricos de compras de ingredientes por correo electrónico. Similar al sistema de reportes de pedidos, permite filtrar por fecha y proveedor.

## Características

- ✅ **Reportes parametrizados**: Filtra compras por rango de fechas
- ✅ **Filtro por proveedor**: Opción de filtrar por un proveedor específico o incluir todos
- ✅ **Múltiples destinatarios**: Envía el reporte a varios correos a la vez
- ✅ **Vista previa**: Visualiza el reporte antes de enviarlo
- ✅ **Formato HTML**: Correos profesionales con diseño responsive
- ✅ **Detalles completos**: Incluye ingredientes, cantidades, precios, estado y más
- ✅ **Resumen estadístico**: Muestra totales, cantidad de compras y montos

## Acceso al Sistema

### URL Principal
```
http://tu-dominio.com/admin/correos/reportes-compras
```

### Menú de Navegación
1. Inicia sesión en el sistema
2. Ve a: **Admin → Correos → Reportes de Compras**

## Uso del Sistema

### 1. Acceder al Formulario

Navega a la ruta `/admin/correos/reportes-compras` donde encontrarás el formulario de envío.

### 2. Llenar el Formulario

#### Campos Requeridos:

- **Destinatarios** (*obligatorio*): 
  - Introduce uno o más correos electrónicos separados por comas
  - Ejemplo: `admin@pizzeriabambino.com, gerente@pizzeriabambino.com`

- **Fecha Inicial** (*obligatorio*):
  - Fecha de inicio del período a consultar
  - Por defecto: Primer día del mes actual

- **Fecha Final** (*obligatorio*):
  - Fecha final del período a consultar
  - Por defecto: Fecha actual
  - Debe ser posterior o igual a la fecha inicial

#### Campos Opcionales:

- **Filtrar por Proveedor**:
  - Selecciona un proveedor específico del menú desplegable
  - O deja en "Todos los proveedores" para incluir todas las compras
  - El selector muestra: Nombre del proveedor (RUC)

- **Asunto Personalizado**:
  - Permite personalizar el asunto del correo
  - Si se deja vacío, se genera automáticamente con el formato:
    - `Reporte de Compras - Pizzería Bambino (dd/mm/aaaa - dd/mm/aaaa)`
    - Si hay proveedor: `Reporte de Compras - Pizzería Bambino - Proveedor: [Nombre] (dd/mm/aaaa - dd/mm/aaaa)`

### 3. Acciones Disponibles

#### Vista Previa
- Click en el botón **"Vista Previa"**
- Abre un modal mostrando cómo se verá el correo
- Útil para verificar los datos antes de enviar

#### Test de Conexión
- Click en el botón **"Test Conexión"**
- Verifica que el servidor de correo esté configurado correctamente
- Muestra el host y puerto configurados

#### Enviar Correo
- Click en el botón **"Enviar Correo"**
- Envía el reporte a todos los destinatarios especificados
- Muestra mensaje de éxito con la cantidad de compras encontradas

## Contenido del Reporte

### Información de Resumen

- Período consultado (fecha inicial - fecha final)
- Proveedor (si se filtró por uno específico)
- Total de compras encontradas
- Monto total acumulado
- Fecha y hora de generación del reporte

### Detalle de cada Compra

Por cada compra se muestra:

**Cabecera de la Compra:**
- Número de compra
- Fecha de la compra

**Tabla de Ingredientes:**
- Nombre del ingrediente
- Categoría
- Cantidad y unidad de medida
- Precio unitario
- Subtotal
- Observaciones del detalle

**Información Adicional:**
- Proveedor
- Estado de la compra (Pendiente/Completada/Cancelada)
- Tipo de compra (Contado/Crédito)
- Número de factura
- Almacén de destino
- Fecha de entrega (si aplica)
- Usuario que registró la compra
- Observaciones generales

**Resumen Final:**
- Total general de todas las compras
- Cantidad total de compras en el período

## Archivos del Sistema

### Controlador
```
app/Http/Controllers/EmailController.php
```

Métodos principales:
- `mostrarFormularioCompras()`: Muestra el formulario de envío
- `enviarReporteCompras()`: Procesa y envía el reporte por correo
- `previewReporteCompras()`: Genera vista previa del reporte

### Mailable
```
app/Mail/ReporteCompras.php
```

Clase responsable de generar el correo electrónico con los datos del reporte.

### Vistas

**Formulario:**
```
resources/views/emails/formulario-compras.blade.php
```

**Email HTML:**
```
resources/views/emails/reporte-compras.blade.php
```

### Rutas

Definidas en `routes/web.php`:

```php
// Formulario
Route::get('correos/reportes-compras', [EmailController::class, 'mostrarFormularioCompras'])
    ->name('email.formulario-compras');

// Enviar reporte
Route::post('correos/enviar-reporte-compras', [EmailController::class, 'enviarReporteCompras'])
    ->name('email.enviar-reporte-compras');

// Vista previa
Route::get('correos/preview-reporte-compras', [EmailController::class, 'previewReporteCompras'])
    ->name('email.preview-reporte-compras');
```

## Requisitos

### Configuración de Correo

Asegúrate de tener configurado el servidor SMTP en tu archivo `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=tu-servidor-smtp
MAIL_PORT=587
MAIL_USERNAME=tu-usuario
MAIL_PASSWORD=tu-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@pizzeriabambino.com
MAIL_FROM_NAME="Pizzería Bambino"
```

### Modelos Relacionados

- `App\Models\Compra`
- `App\Models\DetalleCompra`
- `App\Models\Proveedor`
- `App\Models\Ingrediente`
- `App\Models\Almacenes`
- `App\Models\User`

## Validaciones

### Validaciones del Formulario

- **Destinatarios**: Requerido, formato de email válido
- **Fecha Inicial**: Requerida, formato fecha válido
- **Fecha Final**: Requerida, debe ser mayor o igual a fecha inicial
- **Proveedor**: Opcional, debe existir en la base de datos si se especifica
- **Asunto**: Opcional, máximo 255 caracteres

### Validaciones en el Envío

- Se valida el formato de cada email en la lista de destinatarios
- Se verifica la disponibilidad del servidor SMTP
- Se valida la existencia de las relaciones (proveedor, ingredientes, etc.)

## Manejo de Errores

### Mensajes de Error Comunes

1. **"Debe especificar al menos un destinatario"**
   - Solución: Introduce al menos un correo electrónico en el campo destinatarios

2. **"El email '[email]' no tiene un formato válido"**
   - Solución: Verifica que todos los correos tengan formato correcto

3. **"La fecha de fin debe ser posterior o igual a la fecha de inicio"**
   - Solución: Ajusta las fechas para que sean coherentes

4. **"El proveedor seleccionado no es válido"**
   - Solución: Selecciona un proveedor de la lista o deja en "Todos"

5. **"Ocurrió un error al enviar el correo"**
   - Solución: Verifica la configuración del servidor SMTP
   - Usa el botón "Test Conexión" para diagnosticar

## Ejemplos de Uso

### Ejemplo 1: Reporte Completo Mensual

```
Destinatarios: contabilidad@pizzeriabambino.com
Fecha Inicial: 01/11/2024
Fecha Final: 30/11/2024
Proveedor: [Todos los proveedores]
```

Resultado: Reporte con todas las compras de noviembre a todos los proveedores.

### Ejemplo 2: Reporte de Proveedor Específico

```
Destinatarios: gerente@pizzeriabambino.com, admin@pizzeriabambino.com
Fecha Inicial: 01/10/2024
Fecha Final: 31/10/2024
Proveedor: Distribuidora La Paz S.R.L.
```

Resultado: Reporte solo con compras del proveedor seleccionado en octubre.

### Ejemplo 3: Reporte Semanal Rápido

```
Destinatarios: admin@pizzeriabambino.com
Fecha Inicial: 04/11/2024
Fecha Final: 10/11/2024
Proveedor: [Todos los proveedores]
```

Resultado: Reporte de compras de la semana actual.

## Diferencias con Reporte de Pedidos

| Característica | Reporte Pedidos | Reporte Compras |
|----------------|-----------------|------------------|
| Filtro principal | Cliente | Proveedor |
| Campo de fecha | `fecha` | `fecha_compra` |
| Incluye detalles | Items del menú | Ingredientes |
| Estados | Pendiente/Entregado/Cancelado | Pendiente/Completada/Cancelada |
| Información adicional | Repartidor, ubicación | Almacén, factura, entrega |
| Color tema | Rojo (#dc3545) | Azul (#007bff) |
| Ruta | `/admin/correos/reportes` | `/admin/correos/reportes-compras` |

## Soporte

Para problemas o dudas sobre el sistema de reportes:

1. Verifica la configuración del servidor SMTP
2. Revisa los logs de Laravel en `storage/logs/laravel.log`
3. Usa el botón "Test Conexión" para diagnosticar problemas de correo
4. Verifica que los proveedores estén activos en el sistema

## Cambios Implementados

### Archivos Creados

1. ✅ `app/Mail/ReporteCompras.php` - Clase Mailable
2. ✅ `resources/views/emails/formulario-compras.blade.php` - Vista del formulario
3. ✅ `resources/views/emails/reporte-compras.blade.php` - Vista del email
4. ✅ `REPORTE_COMPRAS_EMAIL.md` - Documentación

### Archivos Modificados

1. ✅ `app/Http/Controllers/EmailController.php` - Añadidos métodos para compras
2. ✅ `routes/web.php` - Añadidas rutas para reportes de compras

## Notas Adicionales

- El sistema utiliza el mismo servidor SMTP configurado para pedidos
- Los reportes se generan en tiempo real (no se almacenan)
- El formato del email es responsive y se ve bien en móviles
- Se puede enviar a múltiples destinatarios simultáneamente
- La vista previa no envía correos, solo muestra cómo se verá

---

**Desarrollado para**: Pizzería Bambino  
**Fecha de Implementación**: Noviembre 2024  
**Versión**: 1.0