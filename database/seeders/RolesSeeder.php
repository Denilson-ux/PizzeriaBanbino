<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $rolAdmin = Role::create(['name' => 'Administrador']);
        $rolCajero = Role::create(['name' => 'Cajero']);
        $rolRepartidor = Role::create(['name' => 'Repartidor']);
        $rolCliente = Role::create(['name' => 'Cliente']);

        // Crear permisos
        $permisoVentas = Permission::create(['name' => 'ventas']);
        $permisoPedidos = Permission::create(['name' => 'pedidos']);
        $permisoMispedidos = Permission::create(['name' => 'mispedidos']);
        $permisoItems = Permission::create(['name' => 'items']);
        $permisoUsuarios = Permission::create(['name' => 'usuarios']);
        $permisoVehiculos = Permission::create(['name' => 'vehiculos']);
        $permisoCorreo = Permission::create(['name' => 'correo']);

        // Asignar permisos a roles
        $rolAdmin->givePermissionTo([$permisoVentas, 
                                    $permisoPedidos, 
                                    $permisoMispedidos, 
                                    $permisoItems, 
                                    $permisoUsuarios, 
                                    $permisoVehiculos, 
                                    $permisoCorreo
                                ]);

        $rolCajero->givePermissionTo([$permisoVentas, 
                                        $permisoPedidos, 
                                        $permisoItems, 
                                        $permisoVehiculos, 
                                        $permisoCorreo
                                    ]);

        $rolRepartidor->givePermissionTo([$permisoMispedidos, 
                                        $permisoCorreo
                                    ]);



        // $role = Role::create(['name' => 'Administrador']);
        // $permission = Permission::create(['name' => 'Venta2']);

        // $permission->syncRoles($role);

        // $user = User::find(1);

        // $user->assignRole($role);
    }
}
