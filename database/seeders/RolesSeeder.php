<?php

namespace Database\Seeders;

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
        // Crear roles si no existen
        $rolAdmin = Role::firstOrCreate(['name' => 'Administrador'], ['guard_name' => 'web']);
        $rolCajero = Role::firstOrCreate(['name' => 'Cajero'], ['guard_name' => 'web']);
        $rolRepartidor = Role::firstOrCreate(['name' => 'Repartidor'], ['guard_name' => 'web']);
        $rolCliente = Role::firstOrCreate(['name' => 'Cliente'], ['guard_name' => 'web']);

        // Mapear permisos según nueva estructura (no crear, solo obtener existentes)
        $permisos = Permission::whereIn('name', [
            'ventas', 'pedidos', 'compras', 'proveedores', 'almacenes', 'ingredientes',
            'items_menu', 'personas', 'usuarios', 'vehiculos', 'restaurante'
        ])->get()->keyBy('name');

        // Asignaciones de ejemplo (ajusta según tus necesidades reales)
        $rolAdmin->syncPermissions($permisos->values());

        $rolCajero->syncPermissions([
            $permisos['ventas'] ?? null,
            $permisos['pedidos'] ?? null,
            $permisos['usuarios'] ?? null,
        ]);

        $rolRepartidor->syncPermissions([
            $permisos['pedidos'] ?? null,
        ]);

        $rolCliente->syncPermissions([
            // general sin permisos especiales del backoffice
        ]);
    }
}
