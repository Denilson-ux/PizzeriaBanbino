# Sistema de Correos - Pizzería Bambino

Este sistema permite enviar reportes de pedidos por correo electrónico usando un servidor de correo local configurado en VirtualBox.

## 🚀 Características

- ✓ **Reportes parametrizables**: Fecha inicial, fecha final y cliente específico
- ✓ **Múltiples destinatarios**: Envío a varios correos simultáneamente
- ✓ **Vista previa**: Visualización del email antes del envío
- ✓ **Test de conexión**: Verificación del servidor de correo
- ✓ **Interfaz amigable**: Formulario web con validaciones
- ✓ **Email responsivo**: Diseño adaptativo para todos los dispositivos

## 🛠️ Configuración del Servidor

### 1. Configuración de Red VirtualBox

Según las imágenes proporcionadas, el servidor de correo está configurado en:
- **IP**: `192.168.1.42`
- **Puerto**: `25` (SMTP estándar)
- **DNS**: `192.168.1.42`
- **Dominio**: `tecnoweb.net`

### 2. Configuración Laravel

Asegúrate de que tu archivo `.env` contenga:

```env
# Configuración de correo
MAIL_MAILER=smtp
MAIL_HOST=192.168.1.42
MAIL_PORT=25
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="pizzeria@tecnoweb.net"
MAIL_FROM_NAME="Pizzería Bambino"
```

### 3. Configuración DNS/Hosts

Si tienes problemas de resolución DNS, agrega esta línea al archivo `/etc/hosts` (Linux/Mac) o `C:\Windows\System32\drivers\etc\hosts` (Windows):

```
192.168.1.42    tecnoweb.net
192.168.1.42    correo.tecnoweb.net
```

## 💫 Uso del Sistema

### Acceso al Formulario

Navega a: `http://tu-dominio/admin/correos/reportes`

### Parámetros Configurables

1. **Destinatarios** (*requerido*)
   - Lista de emails separados por comas
   - Ejemplo: `juan@ejemplo.com, maria@ejemplo.com`

2. **Fecha Inicial** (*requerido*)
   - Fecha de inicio del reporte
   - Por defecto: primer día del mes actual

3. **Fecha Final** (*requerido*)
   - Fecha de fin del reporte
   - Por defecto: fecha actual
   - Debe ser posterior o igual a la fecha inicial

4. **Cliente** (*opcional*)
   - Selector de cliente específico
   - Si no se selecciona, incluye todos los pedidos

5. **Asunto Personalizado** (*opcional*)
   - Si se deja vacío, se genera automáticamente

### Funciones Adicionales

- **Vista Previa**: Botón para ver cómo se verá el email
- **Test Conexión**: Verificar que el servidor de correo esté accesible
- **Validaciones**: Campos obligatorios y formatos de email

## 📧 Contenido del Reporte

### Información General
- Período del reporte
- Cliente filtrado (si aplica)
- Total de pedidos encontrados
- Monto total
- Fecha de generación

### Detalles de Pedidos
- ID del pedido
- Fecha y hora
- Información del cliente
- Estado del pedido (con colores)
- Monto
- Método de pago
- Repartidor asignado

### Diseño
- Logo de Pizzería Bambino
- Colores corporativos
- Tabla responsiva
- Estados con badges de colores
- Totalización al final

## 🔧 Solución de Problemas

### Error de Conexión SMTP

1. **Verificar IP y puerto**:
   ```bash
   telnet 192.168.1.42 25
   ```

2. **Comprobar configuración**:
   - Usar el botón "Test Conexión" en el formulario
   - Verificar logs de Laravel: `tail -f storage/logs/laravel.log`

3. **Firewall/Antivirus**:
   - Asegurar que el puerto 25 esté abierto
   - Verificar que no haya bloqueos

### Problemas de DNS

1. **Verificar resolución**:
   ```bash
   nslookup tecnoweb.net
   ```

2. **Configurar hosts localmente** (ver sección de configuración)

### Email no llega

1. **Verificar carpeta de spam/correo basura**
2. **Comprobar logs del servidor de correo**
3. **Verificar dirección FROM configurada**

## 📝 Rutas del Sistema

```php
// Formulario para enviar correos
GET /admin/correos/reportes

// Enviar reporte
POST /admin/correos/enviar-reporte

// Vista previa
GET /admin/correos/preview-reporte

// Test de conexión
GET /admin/correos/test-conexion
```

## 📊 Archivos del Sistema

```
app/
├── Mail/
│   └── ReportePedidos.php          # Mailable principal
├── Http/Controllers/
│   └── EmailController.php         # Controlador de correos
├── Models/
    ├── Cliente.php                 # Modelo actualizado
    └── Repartidor.php              # Modelo actualizado

resources/views/emails/
├── reporte-pedidos.blade.php       # Vista del email
└── formulario-envio.blade.php      # Formulario web

config/
└── mail.php                        # Configuración de correo
```

## ⚙️ Personalización

### Cambiar Diseño del Email

Edita: `resources/views/emails/reporte-pedidos.blade.php`

### Agregar Campos al Reporte

1. Modificar el controlador `EmailController.php`
2. Actualizar la consulta de pedidos
3. Modificar la vista del email

### Cambiar Servidor de Correo

1. Actualizar archivo `.env`
2. Modificar `config/mail.php` si es necesario
3. Probar conexión

## 🔒 Seguridad

- ✅ Validación de parámetros de entrada
- ✅ Protección CSRF en formularios
- ✅ Autenticación requerida (middleware `auth`)
- ✅ Sanitización de datos
- ✅ Logs de errores

## 🔄 Actualizaciones Futuras

### Posibles Mejoras
- [ ] Programación de envíos (cron jobs)
- [ ] Plantillas de email personalizables
- [ ] Exportación a PDF adjunto
- [ ] Notificaciones push
- [ ] Historial de emails enviados
- [ ] Estadísticas de apertura

---

© 2025 Pizzería Bambino - Sistema de Correos Electrónicos