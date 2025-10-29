<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FixPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos que faltan
        $permisos = [
            'clientes',
            'repartidores',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web'
            ]);
            echo "Permiso '{$permiso}' creado/verificado\n";
        }

        // Asignar todos los permisos al rol Administrador
        $adminRole = Role::where('name', 'Administrador')->first();
        
        if ($adminRole) {
            foreach ($permisos as $permiso) {
                $permission = Permission::where('name', $permiso)->where('guard_name', 'web')->first();
                if ($permission && !$adminRole->hasPermissionTo($permiso)) {
                    $adminRole->givePermissionTo($permission);
                    echo "Permiso '{$permiso}' asignado al rol Administrador\n";
                }
            }
        } else {
            echo "Rol 'Administrador' no encontrado\n";
        }
        
        echo "\n✅ Permisos corregidos exitosamente!\n";
    }
}