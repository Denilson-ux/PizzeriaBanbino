# Configuración Postfix + BIND9 para Pizzería Bambino

## 📸 Situación Actual

- ✅ **BIND9 DNS Server**: Ya configurado en VirtualBox (192.168.1.42)
- ❌ **SMTP Server**: Falta instalar Postfix para el correo
- ✅ **Red**: Funcionando correctamente (ping OK)
- ❌ **Puerto 25**: No responde (TcpTestSucceeded: False)

## 🚀 Solución: Instalar Postfix en la misma VM

### Paso 1: Conectarse a la VM con BIND9

```bash
# SSH o acceso directo a la VM
ssh usuario@192.168.1.42
# o acceder directamente desde VirtualBox
```

### Paso 2: Instalar Postfix

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Postfix
sudo apt install postfix

# Durante la instalación:
# 1. Seleccionar "Internet Site"
# 2. System mail name: tecnoweb.net
```

### Paso 3: Configurar Postfix Básico

```bash
# Editar configuración principal
sudo nano /etc/postfix/main.cf
```

**Contenido para /etc/postfix/main.cf:**
```config
# Hostname del servidor
myhostname = tecnoweb.net

# Dominio
mydomain = tecnoweb.net

# Origen de correos
myorigin = $mydomain

# Interfaces donde escucha (IMPORTANTE)
inet_interfaces = all
inet_protocols = ipv4

# Destinos que maneja este servidor
mydestination = $myhostname, tecnoweb.net, localhost.tecnoweb.net, localhost

# Redes que pueden usar este servidor como relay
mynetworks = 192.168.1.0/24, 127.0.0.0/8

# Configuración de relay
smtpd_relay_restrictions = permit_mynetworks, reject_unauth_destination

# Configuración de bandeja
home_mailbox = Maildir/

# Tamaño máximo de mensaje (50MB)
message_size_limit = 52428800

# No requerir autenticación para redes locales
smtpd_sasl_auth_enable = no
```

### Paso 4: Configurar DNS (Registros MX)

```bash
# Editar zona DNS existente
sudo nano /etc/bind/db.tecnoweb.net
```

**Agregar estos registros a tu zona DNS:**
```dns
; Zona tecnoweb.net
$TTL    604800
@       IN      SOA     tecnoweb.net. admin.tecnoweb.net. (
                              2         ; Serial
                         604800         ; Refresh
                          86400         ; Retry
                        2419200         ; Expire
                         604800 )       ; Negative Cache TTL

; Servidores de nombres
@       IN      NS      tecnoweb.net.

; Registro A del servidor
@       IN      A       192.168.1.42
tecnoweb.net.   IN      A       192.168.1.42

; Registro MX (Mail Exchange) - IMPORTANTE
@       IN      MX      10      tecnoweb.net.

; CNAME para mail
mail    IN      CNAME   tecnoweb.net.
pizzeria IN     CNAME   tecnoweb.net.
```

### Paso 5: Reiniciar Servicios

```bash
# Verificar configuración Postfix
sudo postfix check

# Reiniciar Postfix
sudo systemctl restart postfix
sudo systemctl enable postfix

# Verificar configuración BIND
sudo named-checkconf
sudo named-checkzone tecnoweb.net /etc/bind/db.tecnoweb.net

# Reiniciar BIND
sudo systemctl restart bind9

# Verificar que ambos servicios estén corriendo
sudo systemctl status postfix
sudo systemctl status bind9
```

### Paso 6: Configurar Firewall

```bash
# Permitir puerto SMTP (25) y DNS (53)
sudo ufw allow 25/tcp
sudo ufw allow 53/tcp
sudo ufw allow 53/udp
sudo ufw status
```

### Paso 7: Verificar Configuración

```bash
# Verificar que Postfix escuche en puerto 25
sudo ss -tlnp | grep :25
# Resultado esperado: 0.0.0.0:25

# Verificar que BIND escuche en puerto 53
sudo ss -tlnp | grep :53

# Test local de SMTP
telnet localhost 25
# Debería mostrar: 220 tecnoweb.net ESMTP Postfix
```

## 🗺️ Estructura de Archivos BIND9

```bash
# Archivos principales de configuración
/etc/bind/
├── named.conf                 # Configuración principal
├── named.conf.local           # Zonas locales
├── named.conf.options         # Opciones globales
└── db.tecnoweb.net            # Archivo de zona
```

### Contenido para named.conf.local:
```bind
// Zona forward
zone "tecnoweb.net" {
    type master;
    file "/etc/bind/db.tecnoweb.net";
};

// Zona reverse (opcional)
zone "1.168.192.in-addr.arpa" {
    type master;
    file "/etc/bind/db.192.168.1";
};
```

## 🧪 Testing Completo

### Desde la VM (localhost):
```bash
# Test DNS
nslookup tecnoweb.net localhost
nslookup mail.tecnoweb.net localhost

# Test MX record
dig MX tecnoweb.net @localhost

# Test SMTP
telnet localhost 25
```

### Desde Windows (192.168.1.35):
```powershell
# Test DNS
nslookup tecnoweb.net 192.168.1.42

# Test SMTP
Test-NetConnection 192.168.1.42 -Port 25
# Debería mostrar: TcpTestSucceeded: True
```

## 🌐 Configuración Laravel Final

**Archivo .env:**
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

**Comandos Laravel:**
```bash
# Limpiar caché
php artisan config:clear
php artisan cache:clear

# Test desde tinker
php artisan tinker
>>> use Illuminate\Support\Facades\Mail;
>>> Mail::raw('Test desde Laravel', function($m) { $m->to('test@tecnoweb.net')->subject('Test'); });
```

## 🐞 Solución de Problemas

### Si Postfix no inicia:
```bash
# Ver logs detallados
sudo journalctl -u postfix -f

# Verificar configuración
sudo postfix check

# Verificar permisos
sudo chown -R postfix:postfix /var/spool/postfix
```

### Si DNS no resuelve:
```bash
# Verificar sintaxis
sudo named-checkconf
sudo named-checkzone tecnoweb.net /etc/bind/db.tecnoweb.net

# Ver logs
sudo journalctl -u bind9 -f
```

### Logs Útiles:
```bash
# Postfix
sudo tail -f /var/log/mail.log

# BIND9
sudo tail -f /var/log/syslog | grep named

# Sistema general
sudo journalctl -f
```

## 📝 Verificación Final

Después de la configuración completa:

1. ✅ DNS resuelve tecnoweb.net
2. ✅ Registro MX apunta al servidor correcto
3. ✅ Puerto 25 responde desde Windows
4. ✅ Laravel puede enviar correos
5. ✅ Formulario web funciona correctamente

---

**Nota**: Esta configuración es para desarrollo/testing local. Para producción se recomendaría configurar autenticación SASL y TLS.