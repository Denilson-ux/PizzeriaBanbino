<?php

namespace App\Services;

use App\Models\Almacen;
use App\Models\Ingrediente;
use App\Models\ItemMenu;
use App\Models\MovimientoAlmacen;
use App\Models\NotaVenta;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;
use Exception;

class VentaService
{
    /**
     * Procesar una venta completa con descuento automático de ingredientes
     * 
     * @param array $datosVenta
     * @param array $productosVenta [['id_item_menu' => 1, 'cantidad' => 2, 'precio_unitario' => 25.50], ...]
     * @return NotaVenta
     * @throws Exception
     */
    public function procesarVenta($datosVenta, $productosVenta)
    {
        return DB::transaction(function () use ($datosVenta, $productosVenta) {
            
            // 1. Validar disponibilidad de ingredientes para todos los productos
            $this->validarDisponibilidadIngredientes($productosVenta);
            
            // 2. Crear la nota de venta
            $notaVenta = NotaVenta::create([
                'id_pedido' => $datosVenta['id_pedido'] ?? null,
                'id_cliente' => $datosVenta['id_cliente'] ?? null,
                'fecha_venta' => now(),
                'subtotal' => 0, // Se calculará después
                'descuento' => $datosVenta['descuento'] ?? 0,
                'total' => 0, // Se calculará después
                'estado' => 'completada'
            ]);
            
            $subtotal = 0;
            
            // 3. Procesar cada producto de la venta
            foreach ($productosVenta as $producto) {
                $itemMenu = ItemMenu::find($producto['id_item_menu']);
                
                if (!$itemMenu) {
                    throw new Exception("Producto no encontrado: ID {$producto['id_item_menu']}");
                }
                
                // Crear detalle de venta
                $detalleVenta = DetalleVenta::create([
                    'id_nota_venta' => $notaVenta->id_nota_venta,
                    'id_item_menu' => $producto['id_item_menu'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'subtotal' => $producto['cantidad'] * $producto['precio_unitario']
                ]);
                
                $subtotal += $detalleVenta->subtotal;
                
                // 4. Descontar ingredientes del almacén para este producto
                $this->descontarIngredientesDeReceta(
                    $itemMenu, 
                    $producto['cantidad'], 
                    $notaVenta->id_nota_venta
                );
            }
            
            // 5. Actualizar totales de la venta
            $descuento = $datosVenta['descuento'] ?? 0;
            $total = $subtotal - $descuento;
            
            $notaVenta->update([
                'subtotal' => $subtotal,
                'total' => $total
            ]);
            
            return $notaVenta;
        });
    }
    
    /**
     * Validar que hay suficientes ingredientes para todos los productos
     */
    private function validarDisponibilidadIngredientes($productosVenta)
    {
        $ingredientesRequeridos = [];
        
        // Calcular total de ingredientes requeridos
        foreach ($productosVenta as $producto) {
            $itemMenu = ItemMenu::with('ingredientes')->find($producto['id_item_menu']);
            
            if (!$itemMenu) {
                throw new Exception("Producto no encontrado: ID {$producto['id_item_menu']}");
            }
            
            foreach ($itemMenu->ingredientes as $ingrediente) {
                $cantidadNecesaria = $ingrediente->pivot->cantidad_necesaria * $producto['cantidad'];
                $idIngrediente = $ingrediente->id_ingrediente;
                
                if (isset($ingredientesRequeridos[$idIngrediente])) {
                    $ingredientesRequeridos[$idIngrediente] += $cantidadNecesaria;
                } else {
                    $ingredientesRequeridos[$idIngrediente] = $cantidadNecesaria;
                }
            }
        }
        
        // Validar stock disponible
        foreach ($ingredientesRequeridos as $idIngrediente => $cantidadRequerida) {
            $almacen = Almacen::where('id_ingrediente', $idIngrediente)->first();
            
            if (!$almacen) {
                $ingrediente = Ingrediente::find($idIngrediente);
                throw new Exception("No hay registro en almacén para el ingrediente: {$ingrediente->nombre}");
            }
            
            if (!$almacen->tieneStockSuficiente($cantidadRequerida)) {
                throw new Exception(
                    "Stock insuficiente para {$almacen->ingrediente->nombre}. " .
                    "Disponible: {$almacen->stock_actual} {$almacen->unidad_medida}, " .
                    "Requerido: {$cantidadRequerida} {$almacen->unidad_medida}"
                );
            }
        }
    }
    
    /**
     * Descontar ingredientes de una receta del almacén
     */
    private function descontarIngredientesDeReceta(ItemMenu $itemMenu, $cantidadProductos, $idVenta)
    {
        $ingredientes = $itemMenu->ingredientes;
        
        foreach ($ingredientes as $ingrediente) {
            $cantidadADescontar = $ingrediente->pivot->cantidad_necesaria * $cantidadProductos;
            
            // Obtener registro de almacén
            $almacen = $ingrediente->almacen;
            
            if (!$almacen) {
                throw new Exception("No hay registro en almacén para el ingrediente: {$ingrediente->nombre}");
            }
            
            $stockAnterior = $almacen->stock_actual;
            
            // Reducir stock
            $almacen->reducirStock($cantidadADescontar);
            
            // Registrar movimiento de almacén
            MovimientoAlmacen::create([
                'id_ingrediente' => $ingrediente->id_ingrediente,
                'tipo_movimiento' => 'egreso',
                'cantidad' => $cantidadADescontar,
                'unidad_medida' => $almacen->unidad_medida,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $almacen->stock_actual,
                'referencia_tipo' => 'venta',
                'referencia_id' => $idVenta,
                'observaciones' => "Venta de {$cantidadProductos} {$itemMenu->nombre}",
                'usuario_id' => auth()->id(),
                'fecha_movimiento' => now()
            ]);
        }
    }
    
    /**
     * Verificar disponibilidad de un producto específico
     */
    public function verificarDisponibilidadProducto($idItemMenu, $cantidad = 1)
    {
        $itemMenu = ItemMenu::with('ingredientes.almacen')->find($idItemMenu);
        
        if (!$itemMenu) {
            return [
                'disponible' => false,
                'mensaje' => 'Producto no encontrado'
            ];
        }
        
        $ingredientesFaltantes = [];
        
        foreach ($itemMenu->ingredientes as $ingrediente) {
            $cantidadNecesaria = $ingrediente->pivot->cantidad_necesaria * $cantidad;
            
            if (!$ingrediente->almacen) {
                $ingredientesFaltantes[] = $ingrediente->nombre . ' (sin stock registrado)';
                continue;
            }
            
            if (!$ingrediente->almacen->tieneStockSuficiente($cantidadNecesaria)) {
                $disponible = $ingrediente->almacen->stock_actual;
                $requerido = $cantidadNecesaria;
                $ingredientesFaltantes[] = "{$ingrediente->nombre} (disponible: {$disponible}, requerido: {$requerido})";
            }
        }
        
        if (empty($ingredientesFaltantes)) {
            return ['disponible' => true, 'mensaje' => 'Producto disponible'];
        } else {
            return [
                'disponible' => false,
                'mensaje' => 'Ingredientes insuficientes: ' . implode(', ', $ingredientesFaltantes)
            ];
        }
    }
    
    /**
     * Obtener cantidad máxima disponible de un producto
     */
    public function obtenerCantidadMaximaDisponible($idItemMenu)
    {
        $itemMenu = ItemMenu::with('ingredientes.almacen')->find($idItemMenu);
        
        if (!$itemMenu) {
            return 0;
        }
        
        $cantidadMaxima = PHP_INT_MAX;
        
        foreach ($itemMenu->ingredientes as $ingrediente) {
            if (!$ingrediente->almacen) {
                return 0; // Si no hay almacén, no se puede hacer
            }
            
            $cantidadPorIngrediente = $ingrediente->pivot->cantidad_necesaria;
            $stockDisponible = $ingrediente->almacen->stock_actual;
            
            if ($cantidadPorIngrediente > 0) {
                $posiblesUnidades = floor($stockDisponible / $cantidadPorIngrediente);
                $cantidadMaxima = min($cantidadMaxima, $posiblesUnidades);
            }
        }
        
        return $cantidadMaxima === PHP_INT_MAX ? 0 : max(0, $cantidadMaxima);
    }
}