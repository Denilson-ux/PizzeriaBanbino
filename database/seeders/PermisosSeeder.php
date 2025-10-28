<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info("🚀 Iniciando creación de roles y permisos...");
        $this->command->info(str_repeat('=', 50));

        // Crear permisos exactos basados en tu imagen
        $permisos = [
            'Rol',
            'Permiso', 
            'Rol Permiso',
            'Usuario',
            'Asignacion Roles y Permisos',
            'Cliente',
            'Categoria',
            'Producto',
            'Almacen',
            'Producto Almacen',
            'Venta'
        ];

        // Permisos adicionales del sistema
        $permisosExtras = [
            'usuarios',
            'roles', 
            'items',
            'pedidos',
            'mispedidos',
            'ventas',
            'vehiculos',
            'configuracion',
            'reportes'
        ];

        // Combinar todos los permisos
        $todosLosPermisos = array_merge($permisos, $permisosExtras);

        // Crear permisos si no existen
        $this->command->info("🔄 Creando permisos...");
        foreach ($todosLosPermisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web'
            ]);
        }

        // Crear roles principales
        $administrador = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $encargadoVentas = Role::firstOrCreate(['name' => 'Encargado ventas', 'guard_name' => 'web']);
        $cajero = Role::firstOrCreate(['name' => 'Cajero', 'guard_name' => 'web']);
        $repartidor = Role::firstOrCreate(['name' => 'Repartidor', 'guard_name' => 'web']);
        $cliente = Role::firstOrCreate(['name' => 'Cliente', 'guard_name' => 'web']);

        // Asignar permisos a roles
        $administrador->syncPermissions($todosLosPermisos);
        $encargadoVentas->syncPermissions(['Cliente','Categoria','Producto','Almacen','Producto Almacen','Venta','items','ventas']);
        $cajero->syncPermissions(['Cliente','Producto','Venta','items']);
        $repartidor->syncPermissions(['Cliente','pedidos','mispedidos']);
        $cliente->syncPermissions(['Producto']);

        // Crear usuarios demo si no existen y asignar roles
        $edwin = User::firstOrCreate(['email' => 'edwin@pizzeria.com'], [
            'name' => 'Edwin',
            'password' => bcrypt('123456789'),
            'email_verified_at' => now(),
            'estado' => 1
        ]);
        if (!$edwin->hasRole('Administrador')) $edwin->assignRole('Administrador');

        $carlos = User::firstOrCreate(['email' => 'carlos@pizzeria.com'], [
            'name' => 'Carlos',
            'password' => bcrypt('123456789'),
            'email_verified_at' => now(),
            'estado' => 1
        ]);
        if (!$carlos->hasRole('Encargado ventas')) $carlos->assignRole('Encargado ventas');

        // Asignación masiva según id_rol si existe
        User::where('id_rol', 1)->get()->each(function($u){ if(!$u->hasRole('Administrador')) $u->assignRole('Administrador');});
        User::where('id_rol', 2)->get()->each(function($u){ if(!$u->hasRole('Cajero')) $u->assignRole('Cajero');});
        User::where('id_rol', 3)->get()->each(function($u){ if(!$u->hasRole('Repartidor')) $u->assignRole('Repartidor');});

        $this->command->info('✅ Seeder finalizado.');
    }
}
