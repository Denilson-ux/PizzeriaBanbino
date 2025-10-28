# Sistema de Asignación de Roles y Permisos

## 🎯 Descripción

Este sistema permite gestionar de forma visual y mediante formularios la asignación de roles y permisos a los usuarios de la aplicación, reemplazando el método manual por consola.

## 🛠️ Instalación y Configuración

### 1. Ejecutar las Migraciones

```bash
# Publicar las migraciones de Spatie Permission (si no se ha hecho)
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Ejecutar migraciones
php artisan migrate
```

### 2. Poblar la Base de Datos

```bash
# Ejecutar el seeder para crear roles y permisos
php artisan db:seed --class=RolesPermisosSeeder
```

Esto creará:
- **Roles**: Administrador, Encargado ventas
- **Permisos**: Rol, Permiso, Rol Permiso, Usuario, Asignacion Roles y Permisos, Cliente, Categoria, Producto, Almacen, Producto Almacen, Venta
- **Usuarios de prueba**: Edwin (Admin) y Carlos (Encargado)

### 3. Configurar el Modelo User

Asegúrate de que tu modelo `User` tenga el trait necesario:

```php
<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    
    // ... resto del modelo
}
```

## 📺 Acceso al Sistema

### URLs Principales:

- **Lista Principal**: `/admin/asignacion-roles-permisos`
- **Nueva Asignación**: `/admin/asignacion-roles-permisos/create`
- **Editar Asignación**: `/admin/asignacion-roles-permisos/{user}/edit`
- **Ver Detalles**: `/admin/asignacion-roles-permisos/{user}`

### Usuarios de Prueba:

- **Edwin (Administrador)**
  - Email: edwin@pizzeria.com
  - Password: 123456789

- **Carlos (Encargado Ventas)**
  - Email: carlos@pizzeria.com
  - Password: 123456789

## 🎨 Características del Sistema

### 📊 Vista Principal (Index)
- Lista todos los usuarios con sus roles y permisos
- Tabla con DataTables para búsqueda y paginación
- Resumen de roles y permisos disponibles
- Acciones rápidas (Ver, Editar, Eliminar)

### ➕ Formulario de Creación
- Selección de usuario mediante dropdown
- Checkboxes para roles con vista previa de permisos
- Checkboxes para permisos directos adicionales
- Vista previa dinámica de asignaciones actuales

### ✏️ Formulario de Edición
- Muestra las asignaciones actuales del usuario
- Permite modificar roles y permisos
- Distingue entre permisos directos y por rol
- Botón para restaurar valores originales

### 🔍 Vista de Detalles
- Información completa del usuario
- Estadísticas de permisos y roles
- Lista detallada de permisos por rol
- Distinción visual entre permisos directos y por rol

## 🚀 Cómo Usar

### Asignar Roles y Permisos a un Usuario:

1. Ir a `/admin/asignacion-roles-permisos`
2. Hacer clic en "Nueva Asignación"
3. Seleccionar el usuario
4. Marcar los roles deseados
5. (Opcional) Marcar permisos adicionales directos
6. Hacer clic en "Asignar Roles y Permisos"

### Editar Asignaciones Existentes:

1. En la lista principal, hacer clic en el botón de editar (✏️)
2. Modificar roles y permisos según sea necesario
3. Hacer clic en "Actualizar Asignaciones"

### Ver Detalles de un Usuario:

1. En la lista principal, hacer clic en el botón de ver (👁️)
2. Revisar toda la información de permisos y roles

## 📜 API Endpoints Disponibles

```php
// Rutas principales (Resource)
Route::resource('asignacion-roles-permisos', AsignacionRolPermisoController::class);

// APIs auxiliares
Route::get('roles/{role}/permisos', 'getRolePermissions');  // Obtener permisos de un rol
Route::get('users/{user}/assignments', function() {...});   // Obtener asignaciones actuales

// Asignaciones individuales (AJAX)
Route::post('assign-role', 'assignRole');                   // Asignar rol individual
Route::post('remove-role', 'removeRole');                   // Remover rol individual
Route::post('assign-permission', 'assignPermission');       // Asignar permiso individual
Route::post('remove-permission', 'removePermission');       // Remover permiso individual
```

## 🔒 Diferencias Entre Permisos Directos y Por Rol

### Permisos Por Rol:
- Se asignan automáticamente cuando se asigna un rol
- Son heredados del rol
- Se pierden si se remueve el rol
- Más fáciles de gestionar para grupos de usuarios

### Permisos Directos:
- Se asignan específicamente al usuario
- Permanecen aunque se cambien los roles
- Útiles para casos especiales o excepciones
- Tienen prioridad sobre las restricciones de rol

## 🎆 Ejemplos de Uso en Código

### Verificar Permisos en Controladores:

```php
// Verificar si el usuario tiene un permiso
if (auth()->user()->can('Venta')) {
    // El usuario puede gestionar ventas
}

// Verificar si el usuario tiene un rol
if (auth()->user()->hasRole('Administrador')) {
    // El usuario es administrador
}

// Middleware en rutas
Route::middleware(['permission:Usuario'])->group(function () {
    // Solo usuarios con permiso 'Usuario' pueden acceder
});
```

### En Vistas Blade:

```php
@can('Producto')
    <!-- Solo visible para usuarios con permiso 'Producto' -->
    <a href="{{ route('productos.create') }}">Crear Producto</a>
@endcan

@role('Administrador')
    <!-- Solo visible para administradores -->
    <div class="admin-panel">...</div>
@endrole
```

## 🚫 Seguridad y Recomendaciones

1. **Principio de Menor Privilegio**: Asigna solo los permisos mínimos necesarios
2. **Revisión Regular**: Revisa periódicamente las asignaciones
3. **Documentación**: Mantiene documentado quién tiene qué permisos y por qué
4. **Backup**: Siempre respalda antes de hacer cambios masivos
5. **Testing**: Prueba los permisos después de cada asignación

## 🔧 Troubleshooting

### Problema: "Permission does not exist"
```bash
# Limpiar cache de permisos
php artisan permission:cache-reset
```

### Problema: Los permisos no se reflejan
```bash
# Limpiar todos los caches
php artisan cache:clear
php artisan config:clear
php artisan permission:cache-reset
```

### Problema: Error en las vistas
- Verifica que AdminLTE esté instalado correctamente
- Asegúrate de que las rutas estén registradas
- Revisa que el middleware 'auth' esté funcionando

## 📝 Logs y Monitoreo

El sistema registra automáticamente:
- Asignaciones de roles y permisos
- Modificaciones en las asignaciones
- Errores de permisos

Revisa los logs en `storage/logs/laravel.log` para debugging.

---

¡Ahora puedes gestionar roles y permisos a través de una interfaz web fácil de usar en lugar de usar comandos manuales! 🎉