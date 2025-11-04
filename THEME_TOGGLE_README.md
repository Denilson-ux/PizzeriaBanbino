# Sistema de Modo Oscuro/Claro - Pizzería Bambino

Este documento describe la implementación del sistema de cambio de tema (modo oscuro/claro) para la aplicación de Pizzería Bambino.

## Archivos Implementados

### 1. CSS - `resources/css/theme-toggle.css`
- **Propósito**: Define las variables CSS para ambos temas y estilos responsivos
- **Características**:
  - Variables CSS para colores de modo claro y oscuro
  - Transiciones suaves entre temas
  - Compatibilidad con AdminLTE
  - Estilos para cards, tables, forms, buttons, etc.

### 2. JavaScript - `resources/js/theme-toggle.js`
- **Propósito**: Maneja la funcionalidad de cambio de tema
- **Características**:
  - Persistencia en localStorage
  - Detección de preferencias del sistema
  - Creación automática del botón de toggle
  - API global para control programático
  - Eventos personalizados para integración

### 3. Componente Blade - `resources/views/components/theme-toggle.blade.php`
- **Propósito**: Componente reutilizable para el botón de cambio de tema
- **Parámetros**:
  - `position`: 'navbar', 'fixed', 'inline'
  - `size`: 'sm', 'md', 'lg'
  - `class`: clases CSS adicionales

### 4. Layout Actualizado - `resources/views/layouts/app.blade.php`
- **Cambios**:
  - Inclusión de Font Awesome para iconos
  - Carga de CSS y JS del tema
  - Script de inicialización temprana
  - Stacks para estilos y scripts adicionales

### 5. Menú de Navegación - `resources/views/navigation-menu.blade.php`
- **Cambios**:
  - Botón de tema en desktop y mobile
  - Integración responsive
  - Posicionamiento optimizado

## Cómo Usar

### Uso Básico
El sistema se activa automáticamente al cargar cualquier página que use el layout `app.blade.php`.

### Componente de Botón
```php
<!-- Botón en navbar -->
<x-theme-toggle position="navbar" size="md" />

<!-- Botón inline -->
<x-theme-toggle position="inline" size="sm" />

<!-- Botón fijo -->
<x-theme-toggle position="fixed" size="lg" />
```

### Control Programático
```javascript
// Cambiar tema
window.toggleTheme();

// Establecer tema específico
window.setTheme('dark'); // o 'light'

// Obtener tema actual
const currentTheme = window.getCurrentTheme();
```

### Eventos Personalizados
```javascript
// Escuchar cambios de tema
document.addEventListener('themeChanged', function(e) {
    console.log('Nuevo tema:', e.detail.theme);
    // Actualizar componentes específicos
});
```

## Características del Sistema

### 🎨 Temas Disponibles
- **Modo Claro**: Colores claros tradicionales
- **Modo Oscuro**: Colores oscuros para reducir fatiga visual

### 💾 Persistencia
- La preferencia se guarda en `localStorage`
- Se mantiene entre sesiones del navegador
- Detección automática de preferencias del sistema

### 📱 Responsive
- Botón optimizado para desktop y mobile
- Tamaños configurables
- Posicionamiento flexible

### ⚡ Performance
- Carga inmediata del tema para evitar flash
- Transiciones CSS suaves (0.3s)
- Variables CSS para cambios eficientes

### 🔧 Personalización
- Variables CSS fácilmente modificables
- Componente configurable
- API JavaScript extensible

## Variables CSS Principales

```css
:root {
  --bg-primary: #ffffff;      /* Fondo principal */
  --bg-secondary: #f8f9fa;    /* Fondo secundario */
  --text-primary: #212529;    /* Texto principal */
  --text-secondary: #6c757d;  /* Texto secundario */
  --border-color: #dee2e6;    /* Bordes */
  --card-bg: #ffffff;         /* Fondo de cards */
  /* ... más variables */
}

[data-theme="dark"] {
  --bg-primary: #1a1a1a;      /* Fondo principal oscuro */
  --bg-secondary: #2d2d30;    /* Fondo secundario oscuro */
  --text-primary: #ffffff;    /* Texto principal claro */
  /* ... más variables */
}
```

## Compatibilidad

- ✅ Laravel 8+
- ✅ Blade Components
- ✅ AdminLTE (parcial)
- ✅ Tailwind CSS
- ✅ Font Awesome 6.0+
- ✅ Navegadores modernos

## Solución de Problemas

### El tema no se aplica
1. Verificar que `theme-toggle.css` se carga correctamente
2. Comprobar que el JavaScript se ejecuta sin errores
3. Verificar que Font Awesome está disponible

### El botón no aparece
1. Verificar que el componente se incluye correctamente
2. Comprobar la estructura del navbar/layout
3. Verificar estilos CSS no conflictivos

### Los colores no cambian
1. Verificar que los elementos usan variables CSS
2. Comprobar especificidad de selectores
3. Verificar que `data-theme` se aplica al `html`

## Extensión del Sistema

### Agregar Nuevos Colores
```css
:root {
  --nuevo-color: #valor-claro;
}

[data-theme="dark"] {
  --nuevo-color: #valor-oscuro;
}
```

### Crear Componentes Temáticos
```php
<div style="background-color: var(--card-bg); color: var(--text-primary);">
    <!-- Contenido que respeta el tema -->
</div>
```

### Integrar con Otros Frameworks
```javascript
// Reaccionar a cambios de tema
document.addEventListener('themeChanged', function(e) {
    // Actualizar componentes de terceros
    updateChartColors(e.detail.theme);
    updateMapTheme(e.detail.theme);
});
```

## Próximas Mejoras

- [ ] Más variantes de tema (azul, verde, etc.)
- [ ] Integración con configuración de usuario
- [ ] Animaciones más avanzadas
- [ ] Soporte para temas personalizados
- [ ] Mejores contrastes para accesibilidad

---

**Nota**: Este sistema está diseñado para ser fácil de usar y extensible. Si necesitas personalizar algún aspecto, modifica las variables CSS o extiende la clase JavaScript según tus necesidades.