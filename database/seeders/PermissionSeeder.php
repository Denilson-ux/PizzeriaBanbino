<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Estructura de permisos modular para el sistema de pizzería:
     * Cada permiso controla acceso a funcionalidades específicas del sistema
     */
    public function run(): void
    {
        // Limpiar permisos existentes para evitar duplicados
        Permission::query()->delete();
        
        // Permisos del módulo de Ventas
        // Controla "Nueva venta", "Ventas", "Método de pago"
        Permission::create(['name' => 'ventas', 'guard_name' => 'web']);
        
        // Permisos del módulo de Pedidos
        // Controla "Pedidos" y "Mis pedidos"
        Permission::create(['name' => 'pedidos', 'guard_name' => 'web']);
        
        // Permisos del módulo de Compras
        // Controla "Gestionar Compras"
        Permission::create(['name' => 'compras', 'guard_name' => 'web']);
        
        // Permisos del módulo de Proveedores
        // Controla "Gestionar Proveedores"
        Permission::create(['name' => 'proveedores', 'guard_name' => 'web']);
        
        // Permisos del módulo de Almacenes
        // Controla "Gestionar Almacenes"
        Permission::create(['name' => 'almacenes', 'guard_name' => 'web']);
        
        // Permisos del módulo de Ingredientes
        // Controla "Gestionar Ingredientes"
        Permission::create(['name' => 'ingredientes', 'guard_name' => 'web']);
        
        // Permisos del módulo de Items Menú
        // Controla "Catálogo Menú", "Item Menú", "Tipo Item Menú"
        Permission::create(['name' => 'items_menu', 'guard_name' => 'web']);
        
        // Permisos del módulo de Personas
        // Controla "Cliente", "Empleado", "Repartidor"
        Permission::create(['name' => 'personas', 'guard_name' => 'web']);
        
        // Permisos del módulo de Usuarios
        // Controla "Usuarios", "Roles", "Asignación Roles-Permisos"
        Permission::create(['name' => 'usuarios', 'guard_name' => 'web']);
        
        // Permisos del módulo de Vehículos
        // Controla "Vehículos y Tipos de vehículo"
        Permission::create(['name' => 'vehiculos', 'guard_name' => 'web']);
        
        // Permisos del módulo de Restaurante
        // Controla "Restaurante" (configuración)
        Permission::create(['name' => 'restaurante', 'guard_name' => 'web']);
        
        $this->command->info('✅ Permisos creados exitosamente:');
        $this->command->info('   - ventas → Nueva venta, Ventas, Método de pago');
        $this->command->info('   - pedidos → Pedidos y Mis pedidos');
        $this->command->info('   - compras → Gestionar Compras');
        $this->command->info('   - proveedores → Gestionar Proveedores');
        $this->command->info('   - almacenes → Gestionar Almacenes');
        $this->command->info('   - ingredientes → Gestionar Ingredientes');
        $this->command->info('   - items_menu → Catálogo Menú, Item Menú, Tipo Item Menú');
        $this->command->info('   - personas → Cliente, Empleado, Repartidor');
        $this->command->info('   - usuarios → Usuarios, Roles, Asignación Roles-Permisos');
        $this->command->info('   - vehiculos → Vehículos y Tipos de vehículo');
        $this->command->info('   - restaurante → Restaurante (configuración)');
    }
}