<?php

namespace App\Services;

use App\Models\Almacen;
use App\Models\MovimientoAlmacen;
use App\Models\DetallePedido;
use App\Models\DetalleVenta;
use App\Models\Pedido;
use App\Models\NotaVenta;
use Illuminate\Support\Facades\DB;
use Exception;

class AlmacenService
{
    /**
     * Procesar reducción de stock por pedido
     */
    public function procesarPedido($pedidoId)
    {
        try {
            DB::beginTransaction();
            
            $pedido = Pedido::with('detalles.itemMenu')->findOrFail($pedidoId);
            $errores = [];
            
            foreach ($pedido->detalles as $detalle) {
                $almacen = Almacen::where('id_item_menu', $detalle->id_item_menu)->first();
                
                if (!$almacen) {
                    $errores[] = "El producto '{$detalle->itemMenu->nombre}' no está registrado en almacén";
                    continue;
                }
                
                if (!$almacen->hayStockSuficiente($detalle->cantidad)) {
                    $errores[] = "Stock insuficiente para '{$detalle->itemMenu->nombre}'. Stock actual: {$almacen->stock_actual}, solicitado: {$detalle->cantidad}";
                    continue;
                }
                
                // Reducir stock
                $almacen->reducirStock(
                    $detalle->cantidad, 
                    'venta'
                );
                
                // Actualizar el movimiento con referencia al pedido
                $ultimoMovimiento = MovimientoAlmacen::where('id_almacen', $almacen->id_almacen)
                                                   ->orderBy('created_at', 'desc')
                                                   ->first();
                
                if ($ultimoMovimiento) {
                    $ultimoMovimiento->update([
                        'referencia_id' => $pedidoId,
                        'referencia_tipo' => 'pedido',
                        'observaciones' => "Venta - Pedido #{$pedidoId}"
                    ]);
                }
            }
            
            if (!empty($errores)) {
                DB::rollback();
                return ['success' => false, 'errores' => $errores];
            }
            
            DB::commit();
            return ['success' => true, 'mensaje' => 'Stock actualizado correctamente'];
            
        } catch (Exception $e) {
            DB::rollback();
            return ['success' => false, 'errores' => ['Error interno: ' . $e->getMessage()]];
        }
    }
    
    /**
     * Procesar reducción de stock por venta
     */
    public function procesarVenta($notaVentaId)
    {
        try {
            DB::beginTransaction();
            
            $notaVenta = NotaVenta::with('detalles.itemMenu')->findOrFail($notaVentaId);
            $errores = [];
            
            foreach ($notaVenta->detalles as $detalle) {
                $almacen = Almacen::where('id_item_menu', $detalle->id_item_menu)->first();
                
                if (!$almacen) {
                    $errores[] = "El producto no está registrado en almacén";
                    continue;
                }
                
                if (!$almacen->hayStockSuficiente($detalle->cantidad)) {
                    $errores[] = "Stock insuficiente. Stock actual: {$almacen->stock_actual}, solicitado: {$detalle->cantidad}";
                    continue;
                }
                
                // Reducir stock
                $almacen->reducirStock(
                    $detalle->cantidad, 
                    'venta'
                );
                
                // Actualizar el movimiento con referencia a la venta
                $ultimoMovimiento = MovimientoAlmacen::where('id_almacen', $almacen->id_almacen)
                                                   ->orderBy('created_at', 'desc')
                                                   ->first();
                
                if ($ultimoMovimiento) {
                    $ultimoMovimiento->update([
                        'referencia_id' => $notaVentaId,
                        'referencia_tipo' => 'venta',
                        'observaciones' => "Venta directa - Nota #{$notaVentaId}"
                    ]);
                }
            }
            
            if (!empty($errores)) {
                DB::rollback();
                return ['success' => false, 'errores' => $errores];
            }
            
            DB::commit();
            return ['success' => true, 'mensaje' => 'Stock actualizado correctamente'];
            
        } catch (Exception $e) {
            DB::rollback();
            return ['success' => false, 'errores' => ['Error interno: ' . $e->getMessage()]];
        }
    }
    
    /**
     * Revertir movimiento de stock (para cancelaciones)
     */
    public function revertirMovimiento($referenciaId, $referenciaType)
    {
        try {
            DB::beginTransaction();
            
            $movimientos = MovimientoAlmacen::where('referencia_id', $referenciaId)
                                          ->where('referencia_tipo', $referenciaType)
                                          ->where('tipo_movimiento', 'salida')
                                          ->get();
            
            foreach ($movimientos as $movimiento) {
                $almacen = $movimiento->almacen;
                
                // Restaurar el stock
                $almacen->aumentarStock(
                    $movimiento->cantidad, 
                    'devolucion'
                );
                
                // Registrar el movimiento de reversión
                $ultimoMovimiento = MovimientoAlmacen::where('id_almacen', $almacen->id_almacen)
                                                   ->orderBy('created_at', 'desc')
                                                   ->first();
                
                if ($ultimoMovimiento) {
                    $ultimoMovimiento->update([
                        'referencia_id' => $referenciaId,
                        'referencia_tipo' => $referenciaType . '_cancelado',
                        'observaciones' => "Reversión por cancelación de {$referenciaType} #{$referenciaId}"
                    ]);
                }
            }
            
            DB::commit();
            return ['success' => true, 'mensaje' => 'Stock revertido correctamente'];
            
        } catch (Exception $e) {
            DB::rollback();
            return ['success' => false, 'errores' => ['Error al revertir: ' . $e->getMessage()]];
        }
    }
    
    /**
     * Verificar disponibilidad de stock para un pedido
     */
    public function verificarDisponibilidadPedido($detallesPedido)
    {
        $productosNoDisponibles = [];
        
        foreach ($detallesPedido as $detalle) {
            $almacen = Almacen::where('id_item_menu', $detalle['id_item_menu'])->first();
            
            if (!$almacen) {
                $productosNoDisponibles[] = [
                    'producto' => $detalle['nombre'] ?? 'Producto desconocido',
                    'problema' => 'No registrado en almacén'
                ];
                continue;
            }
            
            if (!$almacen->hayStockSuficiente($detalle['cantidad'])) {
                $productosNoDisponibles[] = [
                    'producto' => $detalle['nombre'] ?? 'Producto',
                    'problema' => "Stock insuficiente (disponible: {$almacen->stock_actual})",
                    'solicitado' => $detalle['cantidad'],
                    'disponible' => $almacen->stock_actual
                ];
            }
        }
        
        return [
            'disponible' => empty($productosNoDisponibles),
            'productos_no_disponibles' => $productosNoDisponibles
        ];
    }
    
    /**
     * Obtener productos con stock bajo
     */
    public function getProductosStockBajo()
    {
        return Almacen::stockBajo()
                     ->with(['itemMenu', 'itemMenu.tipoMenu'])
                     ->get()
                     ->map(function ($almacen) {
                         return [
                             'id' => $almacen->id_almacen,
                             'producto' => $almacen->itemMenu->nombre,
                             'tipo' => $almacen->itemMenu->tipoMenu->nombre,
                             'stock_actual' => $almacen->stock_actual,
                             'stock_minimo' => $almacen->stock_minimo,
                             'diferencia' => $almacen->stock_minimo - $almacen->stock_actual
                         ];
                     });
    }
    
    /**
     * Crear registro de almacén para productos nuevos
     */
    public function crearRegistroAlmacen($idItemMenu, $stockInicial = 0)
    {
        try {
            $almacen = Almacen::create([
                'id_item_menu' => $idItemMenu,
                'stock_actual' => $stockInicial,
                'stock_minimo' => 5, // Valor por defecto
                'stock_maximo' => 100, // Valor por defecto
                'unidad_medida' => 'unidad',
                'costo_unitario' => 0
            ]);
            
            if ($stockInicial > 0) {
                MovimientoAlmacen::create([
                    'id_almacen' => $almacen->id_almacen,
                    'tipo_movimiento' => 'entrada',
                    'cantidad' => $stockInicial,
                    'stock_anterior' => 0,
                    'stock_nuevo' => $stockInicial,
                    'motivo' => 'inventario_inicial',
                    'fecha_movimiento' => now(),
                    'usuario_id' => auth()->id() ?? 1,
                    'observaciones' => 'Registro automático de producto nuevo'
                ]);
            }
            
            return ['success' => true, 'almacen' => $almacen];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}