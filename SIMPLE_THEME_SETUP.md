# Modo Oscuro/Claro SIMPLE - Solo Bootstrap

## 🚀 Implementación Rápida (Sin npm)

Esta es la versión simplificada que funciona **directamente sin compilación**. Solo necesitas agregar 2 archivos a tu proyecto.

## 📁 Archivos Necesarios

1. **`public/css/simple-theme-toggle.css`** - Estilos para el modo oscuro/claro
2. **`public/js/simple-theme-toggle.js`** - JavaScript para el toggle

## 🔧 Cómo Implementar

### Paso 1: Incluir los archivos en tu layout principal

En tu archivo de layout principal (donde tienes tu AdminLTE), agrega estas líneas:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Dashboard</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    
    <!-- AGREGAR: CSS del modo oscuro/claro -->
    <link rel="stylesheet" href="{{ asset('css/simple-theme-toggle.css') }}">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- AGREGAR: Botón de cambio de tema (se crea automáticamente) -->
                <!-- El JavaScript creará el botón aquí automáticamente -->
            </ul>
        </nav>

        <!-- Tu contenido actual... -->
        
    </div>

    <!-- Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    
    <!-- AGREGAR: JavaScript del modo oscuro/claro -->
    <script src="{{ asset('js/simple-theme-toggle.js') }}"></script>
</body>
</html>
```

### Paso 2: ¡Ya está listo! 🎉

Eso es todo. El sistema:
- Creará automáticamente el botón de cambio de tema
- Guardará la preferencia en el navegador
- Detectará las preferencias del sistema
- Aplicará transiciones suaves

## 🛠️ Uso Manual (Opcional)

Si quieres crear tu propio botón personalizado:

```html
<!-- Botón personalizado -->
<button onclick="toggleTheme()" class="btn btn-outline-secondary">
    <i class="fas fa-moon"></i> Cambiar Tema
</button>
```

Funciones JavaScript disponibles:
```javascript
// Cambiar tema
toggleTheme();

// Establecer tema específico
setThemeMode('dark');  // o 'light'

// Obtener tema actual
getCurrentTheme(); // devuelve 'dark' o 'light'
```

## 🎨 Personalización de Colores

Puedes modificar los colores editando `public/css/simple-theme-toggle.css`:

```css
/* Cambiar colores del modo oscuro */
[data-bs-theme="dark"] {
  --bs-body-bg: #tu-color-fondo !important;
  --bs-body-color: #tu-color-texto !important;
  --bs-card-bg: #tu-color-tarjetas !important;
  /* ... más colores */
}
```

## ✅ Ventajas de esta Versión

- **Sin compilación**: Funciona directamente
- **Ligero**: Solo 2 archivos pequeños
- **Compatible**: Funciona con AdminLTE y Bootstrap
- **Automático**: Crea el botón automáticamente
- **Persistente**: Recuerda la preferencia
- **Responsive**: Funciona en móvil y desktop

## 📱 Cómo Usar

1. Haz `git pull origin main` en tu proyecto
2. Los archivos ya estarán en `public/css/` y `public/js/`
3. Agrega las 2 líneas de CSS y JS a tu layout
4. ¡Disfruta del modo oscuro/claro!

## 🔍 Ejemplo Visual

El botón aparecerá en la esquina superior derecha de tu navbar con:
- 🌙 Ícono de luna para cambiar a modo oscuro
- ☀️ Ícono de sol para cambiar a modo claro
- Animación suave al hacer hover
- Transiciones suaves entre temas

---

**📝 Nota**: Esta versión es más simple y directa. No requiere conocimientos de npm, webpack, o compilación. Perfecta para proyectos que solo usan Bootstrap y AdminLTE.