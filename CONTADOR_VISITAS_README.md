# Contador de Visitas por Página - PizzeriaBanbino

## 📊 Descripción
Sistema completo de contador de visitas por página implementado con Bootstrap, que rastrea automáticamente las visitas a cada página de la aplicación web y las muestra en el pie de página.

## 🚀 Características

- ✅ **Rastreo automático**: Middleware que registra visitas automáticamente
- ✅ **Contador por página**: Cada página tiene su propio contador independiente
- ✅ **Diseño con Bootstrap**: Interfaz moderna y responsiva
- ✅ **Barra de progreso**: Muestra popularidad relativa de cada página
- ✅ **Formato inteligente**: Números grandes se formatean (1K, 1M)
- ✅ **Filtrado inteligente**: Excluye APIs, admin y requests AJAX
- ✅ **Optimización**: Base de datos indexada para rendimiento

## 📁 Archivos Creados/Modificados

### 1. Migración
```
database/migrations/2024_11_05_000000_create_page_visits_table.php
```

### 2. Modelo
```
app/Models/PageVisit.php
```

### 3. Middleware
```
app/Http/Middleware/TrackPageVisits.php
```

### 4. Componente Blade
```
resources/views/components/page-visit-counter.blade.php
```

### 5. Archivos Modificados
- `app/Http/Kernel.php` - Registro del middleware
- `resources/views/cliente_web/layoutweb.blade.php` - Inclusión del componente

## 🛠️ Instalación

### Paso 1: Ejecutar Migraciones
```bash
php artisan migrate
```

### Paso 2: Limpiar Cache (Opcional)
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📊 Cómo Funciona

### 1. Rastreo Automático
El middleware `TrackPageVisits` se ejecuta en cada request GET que retorna HTML:

- **Registra** cada visita a páginas públicas
- **Excluye** rutas admin, API, AJAX
- **Incrementa** contador en base de datos
- **Actualiza** timestamp de última visita

### 2. Visualización
El componente `page-visit-counter` muestra:

- **Contador actual** de la página
- **Barra de progreso** con popularidad relativa
- **Timestamp** de actualización
- **Diseño responsivo** con Bootstrap

## 🎨 Personalización

### Cambiar Estilos
Edita el archivo `resources/views/components/page-visit-counter.blade.php`:

```css
.page-visit-counter {
    background: tu-color-personalizado;
    border-top: 2px solid tu-border-color;
}
```

### Excluir Rutas Adicionales
En `app/Http/Middleware/TrackPageVisits.php`, modifica el array `$skipRoutes`:

```php
$skipRoutes = [
    'api/*',
    'admin/*',
    'tu-ruta-personalizada/*',
];
```

### Cambiar Posición del Contador
Mueve la línea en cualquier layout:

```blade
@include('components.page-visit-counter')
```

## 📱 Diseño Responsivo

El contador se adapta automáticamente:

- **Desktop**: Información dividida en dos columnas
- **Mobile**: Información apilada verticalmente
- **Tablet**: Diseño híbrido optimizado

## 🔧 Configuración Avanzada

### Personalizar Nombres de Páginas
En el middleware, modifica el método `getPageName()`:

```php
private function getPageName(Request $request): string
{
    $routeName = $request->route()?->getName();
    
    // Mapear nombres personalizados
    $customNames = [
        'home' => 'Página Principal',
        'menu' => 'Menú del Día',
        'about' => 'Sobre Nosotros',
    ];
    
    return $customNames[$routeName] ?? $routeName;
}
```

### Optimización de Rendimiento
Para sitios con mucho tráfico, considera:

1. **Cache Redis**: Cachear contadores por minutos
2. **Queue Jobs**: Procesar incrementos en background
3. **Batch Updates**: Agrupar actualizaciones

## 📈 Estadísticas Disponibles

El modelo `PageVisit` proporciona métodos útiles:

```php
// Obtener contador de una página
$visits = PageVisit::getVisitCount('home');

// Páginas más populares
$topPages = PageVisit::getTopPages(10);

// Incrementar manualmente
PageVisit::incrementVisit('page-name', 'page-url');
```

## 🐛 Solución de Problemas

### Error: Tabla no existe
```bash
php artisan migrate:status
php artisan migrate
```

### Contador no se muestra
1. Verificar que el middleware esté registrado
2. Comprobar que la página usa el layout correcto
3. Revisar logs de Laravel

### Contador no incrementa
1. Verificar que sea un request GET
2. Comprobar que retorne HTML
3. Verificar que no esté en rutas excluidas

## 📊 Ejemplo de Uso

Después de la implementación, cada página mostrará:

```
👁️ Visitas a esta página: 1,234
📊 Popularidad relativa: 85.2%
🕒 Actualizado: 05/11/2024 15:30
```

## 🔒 Estado de Implementación

- [x] ✅ Migración de base de datos
- [x] ✅ Modelo PageVisit
- [x] ✅ Middleware de rastreo
- [x] ✅ Componente Bootstrap
- [x] ✅ Integración en layout web
- [x] ✅ Documentación completa

## 🎯 Próximos Pasos

1. **Ejecutar migración**: `php artisan migrate`
2. **Probar funcionalidad**: Navegar por el sitio
3. **Verificar contadores**: Revisar base de datos
4. **Personalizar estilos**: Ajustar según diseño

---

**¡El contador de visitas está listo para usar! 🎉**

Cada vez que alguien visite una página de tu pizzería, el contador se incrementará automáticamente y se mostrará en el pie de página con un diseño moderno usando Bootstrap.