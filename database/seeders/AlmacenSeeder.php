<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Almacen;
use App\Models\ItemMenu;
use App\Models\MovimientoAlmacen;
use App\Models\User;

class AlmacenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear registros de almacén para todos los productos existentes
        $itemsMenu = ItemMenu::where('estado', 'activo')->get();
        $usuario = User::first(); // Usar el primer usuario para los movimientos iniciales
        
        foreach ($itemsMenu as $item) {
            // Verificar que no exista ya un registro de almacén para este producto
            if (!Almacen::where('id_item_menu', $item->id_item_menu)->exists()) {
                
                // Generar stock inicial aleatorio según el tipo de producto
                $stockInicial = $this->generarStockInicial($item->nombre);
                
                $almacen = Almacen::create([
                    'id_item_menu' => $item->id_item_menu,
                    'stock_actual' => $stockInicial,
                    'stock_minimo' => $this->generarStockMinimo($item->nombre),
                    'stock_maximo' => $this->generarStockMaximo($item->nombre),
                    'unidad_medida' => $this->determinarUnidadMedida($item->nombre),
                    'ubicacion_fisica' => $this->generarUbicacion($item->nombre),
                    'costo_unitario' => $this->generarCostoUnitario($item->precio),
                    'fecha_ultimo_ingreso' => $stockInicial > 0 ? now() : null,
                    'estado' => 'activo'
                ]);
                
                // Crear movimiento inicial si hay stock
                if ($stockInicial > 0) {
                    MovimientoAlmacen::create([
                        'id_almacen' => $almacen->id_almacen,
                        'tipo_movimiento' => 'entrada',
                        'cantidad' => $stockInicial,
                        'stock_anterior' => 0,
                        'stock_nuevo' => $stockInicial,
                        'motivo' => 'inventario_inicial',
                        'fecha_movimiento' => now(),
                        'usuario_id' => $usuario ? $usuario->id : 1,
                        'observaciones' => 'Stock inicial - Seeder de base de datos'
                    ]);
                }
            }
        }
    }
    
    private function generarStockInicial($nombreProducto)
    {
        $nombreLower = strtolower($nombreProducto);
        
        // Stock inicial según tipo de producto
        if (str_contains($nombreLower, 'pizza')) {
            return rand(20, 50); // Pizzas: stock moderado
        } elseif (str_contains($nombreLower, 'bebida') || str_contains($nombreLower, 'gaseosa') || str_contains($nombreLower, 'agua')) {
            return rand(50, 100); // Bebidas: stock alto
        } elseif (str_contains($nombreLower, 'postre') || str_contains($nombreLower, 'helado')) {
            return rand(15, 30); // Postres: stock bajo-medio
        } elseif (str_contains($nombreLower, 'entrada') || str_contains($nombreLower, 'pan')) {
            return rand(25, 40); // Entradas: stock medio
        } else {
            return rand(10, 35); // Otros productos
        }
    }
    
    private function generarStockMinimo($nombreProducto)
    {
        $nombreLower = strtolower($nombreProducto);
        
        if (str_contains($nombreLower, 'pizza')) {
            return rand(5, 10);
        } elseif (str_contains($nombreLower, 'bebida') || str_contains($nombreLower, 'gaseosa')) {
            return rand(10, 20);
        } else {
            return rand(3, 8);
        }
    }
    
    private function generarStockMaximo($nombreProducto)
    {
        $nombreLower = strtolower($nombreProducto);
        
        if (str_contains($nombreLower, 'pizza')) {
            return rand(80, 120);
        } elseif (str_contains($nombreLower, 'bebida') || str_contains($nombreLower, 'gaseosa')) {
            return rand(150, 200);
        } elseif (str_contains($nombreLower, 'postre')) {
            return rand(50, 80);
        } else {
            return rand(60, 100);
        }
    }
    
    private function determinarUnidadMedida($nombreProducto)
    {
        $nombreLower = strtolower($nombreProducto);
        
        if (str_contains($nombreLower, 'bebida') || str_contains($nombreLower, 'gaseosa') || str_contains($nombreLower, 'agua') || str_contains($nombreLower, 'litro')) {
            return 'litro';
        } elseif (str_contains($nombreLower, 'ensalada') || str_contains($nombreLower, 'kg')) {
            return 'kg';
        } else {
            return 'unidad';
        }
    }
    
    private function generarUbicacion($nombreProducto)
    {
        $nombreLower = strtolower($nombreProducto);
        
        if (str_contains($nombreLower, 'bebida') || str_contains($nombreLower, 'gaseosa')) {
            return 'Refrigerador A - Estante ' . rand(1, 3);
        } elseif (str_contains($nombreLower, 'pizza')) {
            return 'Congelador - Sección ' . rand(1, 4);
        } elseif (str_contains($nombreLower, 'postre') || str_contains($nombreLower, 'helado')) {
            return 'Congelador Postres - Gaveta ' . rand(1, 3);
        } else {
            return 'Almacén General - Pasillo ' . chr(65 + rand(0, 3)); // A, B, C, D
        }
    }
    
    private function generarCostoUnitario($precioVenta)
    {
        // El costo será aproximadamente 60-70% del precio de venta
        $porcentajeCosto = rand(60, 70) / 100;
        return round($precioVenta * $porcentajeCosto, 2);
    }
}