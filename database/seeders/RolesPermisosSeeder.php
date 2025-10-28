<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos basados en la imagen proporcionada
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

        // Crear los permisos si no existen
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear roles basados en la imagen
        $administrador = Role::firstOrCreate(['name' => 'Administrador']);
        $encargadoVentas = Role::firstOrCreate(['name' => 'Encargado ventas']);

        // Asignar todos los permisos al Administrador
        $administrador->givePermissionTo(Permission::all());

        // Asignar permisos específicos al Encargado de Ventas
        $encargadoVentas->givePermissionTo([
            'Cliente',
            'Categoria', 
            'Producto',
            'Almacen',
            'Producto Almacen',
            'Venta'
        ]);

        // Crear usuarios de ejemplo si no existen
        $edwin = User::firstOrCreate(
            ['email' => 'edwin@pizzeria.com'],
            [
                'name' => 'Edwin',
                'password' => bcrypt('123456789'),
                'email_verified_at' => now()
            ]
        );

        $carlos = User::firstOrCreate(
            ['email' => 'carlos@pizzeria.com'],
            [
                'name' => 'Carlos',
                'password' => bcrypt('123456789'),
                'email_verified_at' => now()
            ]
        );

        // Asignar roles a los usuarios
        if (!$edwin->hasRole('Administrador')) {
            $edwin->assignRole('Administrador');
        }

        if (!$carlos->hasRole('Encargado ventas')) {
            $carlos->assignRole('Encargado ventas');
        }

        echo "\n✅ Roles y permisos creados exitosamente";
        echo "\n👤 Usuario Edwin (Admin): edwin@pizzeria.com - contraseña: 123456789";
        echo "\n👤 Usuario Carlos (Encargado): carlos@pizzeria.com - contraseña: 123456789";
        echo "\n\n📊 Resumen:";
        echo "\n- Roles creados: " . Role::count();
        echo "\n- Permisos creados: " . Permission::count();
        echo "\n- Usuarios con roles: " . User::role(['Administrador', 'Encargado ventas'])->count();
        echo "\n";
    }
}