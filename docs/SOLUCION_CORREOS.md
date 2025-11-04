# Solución de Problemas - Servidor de Correo SMTP

## 🚨 Error: "Connection could not be established" (#111)

Este error indica que Laravel no puede conectarse al servidor SMTP en la IP `192.168.1.42:25`. Aquí están las posibles causas y soluciones:

## 🔍 Diagnósticos Paso a Paso

### 1. Verificar Conectividad de Red

#### Desde tu máquina host, prueba la conexión:

```bash
# Test básico de conectividad
ping 192.168.1.42

# Test específico del puerto SMTP
telnet 192.168.1.42 25
# o alternativamente:
nc -zv 192.168.1.42 25
```

**Resultado esperado**: Debe conectar y mostrar un banner SMTP como:
```
220 tecnoweb.net ESMTP Postfix
```

### 2. Verificar Configuración de VirtualBox

#### Comprobar tipo de red de la VM:
1. **VirtualBox Manager** → Seleccionar VM → **Configuración** → **Red**
2. Verificar que esté configurada como:
   - **Host-only Adapter** (para comunicación interna)
   - O **Bridged Adapter** (para red externa)

#### Verificar IP de la VM desde dentro:
```bash
# Conectarse a la VM y ejecutar:
ip addr show
# o
ifconfig
```

### 3. Verificar Servicio de Correo en la VM

#### Conectarse a la VM del servidor de correo:
```bash
# Verificar si Postfix está corriendo
sudo systemctl status postfix

# Si no está corriendo, iniciarlo:
sudo systemctl start postfix
sudo systemctl enable postfix

# Verificar que escuche en el puerto 25:
sudo netstat -tlnp | grep :25
# o
sudo ss -tlnp | grep :25
```

### 4. Configuración de Postfix

#### Editar configuración principal:
```bash
sudo nano /etc/postfix/main.cf
```

#### Configuración mínima requerida:
```config
# Hostname del servidor
myhostname = tecnoweb.net

# Dominios que maneja este servidor
mydestination = tecnoweb.net, localhost.localdomain, localhost

# Red desde la cual acepta conexiones (IMPORTANTE)
mynetworks = 192.168.1.0/24, 127.0.0.0/8

# Interfaces donde escucha
inet_interfaces = all

# Protocolo IP
inet_protocols = ipv4

# Permitir relay desde redes locales
smtpd_relay_restrictions = permit_mynetworks, reject_unauth_destination
```

#### Reiniciar Postfix después de cambios:
```bash
sudo systemctl restart postfix
```

### 5. Configuración de Firewall

#### En la VM del servidor (Ubuntu/CentOS):
```bash
# Ubuntu/Debian (UFW)
sudo ufw allow 25/tcp
sudo ufw status

# CentOS/RHEL (firewalld)
sudo firewall-cmd --permanent --add-port=25/tcp
sudo firewall-cmd --reload

# Verificar reglas
sudo firewall-cmd --list-all
```

#### En la máquina host (Windows):
- **Panel de Control** → **Sistema y Seguridad** → **Firewall de Windows Defender**
- **Configuración avanzada** → **Reglas de entrada**
- Crear nueva regla para **Puerto TCP 25**

## 🛠️ Soluciones Alternativas

### Opción 1: Cambiar Puerto SMTP

Si el puerto 25 está bloqueado, cambiar a puerto alternativo:

#### En Postfix:
```bash
# Editar /etc/postfix/master.cf
sudo nano /etc/postfix/master.cf

# Agregar línea:
2525      inet  n       -       y       -       -       smtpd
```

#### En Laravel (.env):
```env
MAIL_PORT=2525
```

### Opción 2: Usar MailHog para Testing

Para desarrollo, instalar MailHog en la VM:

```bash
# Descargar MailHog
wget https://github.com/mailhog/MailHog/releases/download/v1.0.0/MailHog_linux_amd64
sudo mv MailHog_linux_amd64 /usr/local/bin/mailhog
sudo chmod +x /usr/local/bin/mailhog

# Ejecutar MailHog
mailhog
```

#### Configuración Laravel para MailHog:
```env
MAIL_HOST=192.168.1.42
MAIL_PORT=1025
MAIL_ENCRYPTION=null
```

### Opción 3: Configuración Host-Only Network

Si usas Host-Only network, verificar adaptador:

```bash
# En Windows, verificar adaptador VirtualBox:
ipconfig /all

# Buscar "VirtualBox Host-Only Ethernet Adapter"
# Anotar la IP (ej: 192.168.56.1)
```

#### Cambiar configuración en .env:
```env
# Si el adaptador host-only usa rango 192.168.56.x
MAIL_HOST=192.168.56.42
```

## 🧪 Testing de Configuración

### 1. Test Manual con Telnet
```bash
telnet 192.168.1.42 25
# Comandos SMTP básicos:
HELO tecnoweb.net
MAIL FROM: pizzeria@tecnoweb.net
RCPT TO: test@tecnoweb.net
DATA
Subject: Test

Mensaje de prueba
.
QUIT
```

### 2. Test desde Laravel

Usar el botón "Test Conexión" en el formulario o ejecutar:

```bash
php artisan tinker

# En tinker:
use Illuminate\Support\Facades\Mail;
Mail::raw('Test message', function ($message) {
    $message->to('test@tecnoweb.net')
            ->subject('Test from Laravel');
});
```

## 📝 Configuración Final Recomendada

### .env (Laravel):
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

### Verificar en config/mail.php:
```php
'smtp' => [
    'transport' => 'smtp',
    'host' => env('MAIL_HOST', '192.168.1.42'),
    'port' => env('MAIL_PORT', 25),
    'encryption' => env('MAIL_ENCRYPTION', null),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'timeout' => null,
    'local_domain' => env('MAIL_EHLO_DOMAIN'),
    'stream' => [
        'ssl' => [
            'allow_self_signed' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ],
],
```

## 📞 Comandos de Diagnóstico Rápido

```bash
# Limpiar cache de configuración
php artisan config:clear
php artisan cache:clear

# Verificar configuración actual
php artisan tinker
>>> config('mail.mailers.smtp')

# Test de conectividad desde terminal
telnet 192.168.1.42 25

# Verificar logs de Laravel
tail -f storage/logs/laravel.log
```

---

**Nota**: Si sigues teniendo problemas, comparte:
1. Configuración de red de VirtualBox
2. Resultado de `telnet 192.168.1.42 25`
3. Logs de Postfix: `sudo tail -f /var/log/mail.log`