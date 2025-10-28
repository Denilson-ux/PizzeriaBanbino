<?php

namespace App\Services;

use App\Models\Almacen;
use App\Models\Ingrediente;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\MovimientoAlmacen;
use Illuminate\Support\Facades\DB;
use Exception;

class CompraService
{
    /**
     * Procesar una compra completa con aumento automático de stock
     * 
     * @param array $datosCompra
     * @param array $ingredientesCompra [['id_ingrediente' => 1, 'cantidad' => 10, 'precio_unitario' => 5.50], ...]
     * @return Compra
     * @throws Exception
     */
    public function procesarCompra($datosCompra, $ingredientesCompra)
    {
        return DB::transaction(function () use ($datosCompra, $ingredientesCompra) {
            
            // 1. Crear la compra
            $compra = Compra::create([
                'id_proveedor' => $datosCompra['id_proveedor'],
                'numero_factura' => $datosCompra['numero_factura'] ?? null,
                'fecha_compra' => $datosCompra['fecha_compra'] ?? now(),
                'fecha_entrega' => $datosCompra['fecha_entrega'] ?? null,
                'subtotal' => 0, // Se calculará después
                'impuestos' => $datosCompra['impuestos'] ?? 0,
                'total' => 0, // Se calculará después
                'estado' => 'recibido', // Estados: pendiente, recibido, cancelado
                'observaciones' => $datosCompra['observaciones'] ?? null
            ]);
            
            $subtotal = 0;
            
            // 2. Procesar cada ingrediente de la compra
            foreach ($ingredientesCompra as $item) {
                $ingrediente = Ingrediente::find($item['id_ingrediente']);
                
                if (!$ingrediente) {
                    throw new Exception("Ingrediente no encontrado: ID {$item['id_ingrediente']}");
                }
                
                // Crear detalle de compra
                $detalleCompra = DetalleCompra::create([
                    'id_compra' => $compra->id_compra,
                    'id_ingrediente' => $item['id_ingrediente'],
                    'cantidad' => $item['cantidad'],
                    'unidad_medida' => $item['unidad_medida'] ?? $ingrediente->unidad_medida,
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['cantidad'] * $item['precio_unitario'],
                    'fecha_vencimiento' => $item['fecha_vencimiento'] ?? null,
                    'observaciones' => $item['observaciones'] ?? null
                ]);
                
                $subtotal += $detalleCompra->subtotal;
                
                // 3. Aumentar stock en almacén
                $this->aumentarStockIngrediente(
                    $ingrediente,
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $compra->id_compra,
                    $item['fecha_vencimiento'] ?? null
                );
            }
            
            // 4. Actualizar totales de la compra
            $impuestos = $datosCompra['impuestos'] ?? 0;
            $total = $subtotal + $impuestos;
            
            $compra->update([
                'subtotal' => $subtotal,
                'total' => $total
            ]);
            
            return $compra;
        });
    }
    
    /**
     * Aumentar stock de un ingrediente en almacén
     */
    private function aumentarStockIngrediente(Ingrediente $ingrediente, $cantidad, $precioUnitario, $idCompra, $fechaVencimiento = null)
    {
        // Buscar o crear registro en almacén
        $almacen = Almacen::where('id_ingrediente', $ingrediente->id_ingrediente)->first();
        
        if (!$almacen) {
            // Crear nuevo registro en almacén
            $almacen = Almacen::create([
                'id_ingrediente' => $ingrediente->id_ingrediente,
                'stock_actual' => 0,
                'stock_minimo' => 10, // Valor por defecto
                'stock_maximo' => 1000, // Valor por defecto
                'unidad_medida' => $ingrediente->unidad_medida,
                'ubicacion_fisica' => null,
                'costo_unitario_promedio' => $precioUnitario,
                'fecha_vencimiento' => $fechaVencimiento,
                'estado' => 'activo'
            ]);
        }
        
        $stockAnterior = $almacen->stock_actual;
        
        // Aumentar stock y actualizar costo promedio
        $almacen->aumentarStock($cantidad, $precioUnitario);
        
        // Actualizar fecha de vencimiento si es más próxima
        if ($fechaVencimiento && (!$almacen->fecha_vencimiento || $fechaVencimiento < $almacen->fecha_vencimiento)) {
            $almacen->fecha_vencimiento = $fechaVencimiento;
            $almacen->save();
        }
        
        // Registrar movimiento de almacén
        MovimientoAlmacen::create([
            'id_ingrediente' => $ingrediente->id_ingrediente,
            'tipo_movimiento' => 'ingreso',
            'cantidad' => $cantidad,
            'unidad_medida' => $almacen->unidad_medida,
            'costo_unitario' => $precioUnitario,
            'stock_anterior' => $stockAnterior,
            'stock_posterior' => $almacen->stock_actual,
            'referencia_tipo' => 'compra',
            'referencia_id' => $idCompra,
            'observaciones' => "Compra de {$cantidad} {$almacen->unidad_medida} de {$ingrediente->nombre}",
            'usuario_id' => auth()->id(),
            'fecha_movimiento' => now()
        ]);
    }
    
    /**
     * Validar que todos los ingredientes existen
     */
    public function validarIngredientes($ingredientesCompra)
    {
        $ingredientesNoEncontrados = [];
        
        foreach ($ingredientesCompra as $item) {
            $ingrediente = Ingrediente::find($item['id_ingrediente']);
            
            if (!$ingrediente) {
                $ingredientesNoEncontrados[] = "ID: {$item['id_ingrediente']}";
            }
        }
        
        if (!empty($ingredientesNoEncontrados)) {
            throw new Exception('Ingredientes no encontrados: ' . implode(', ', $ingredientesNoEncontrados));
        }
        
        return true;
    }
    
    /**
     * Calcular totales antes de procesar la compra
     */
    public function calcularTotales($ingredientesCompra, $impuestos = 0)
    {
        $subtotal = 0;
        
        foreach ($ingredientesCompra as $item) {
            $subtotal += $item['cantidad'] * $item['precio_unitario'];
        }
        
        $total = $subtotal + $impuestos;
        
        return [
            'subtotal' => $subtotal,
            'impuestos' => $impuestos,
            'total' => $total
        ];
    }
    
    /**
     * Obtener compras por proveedor
     */
    public function obtenerComprasPorProveedor($idProveedor, $fechaInicio = null, $fechaFin = null)
    {
        $query = Compra::where('id_proveedor', $idProveedor)
                      ->with(['detalles.ingrediente', 'proveedor']);
        
        if ($fechaInicio) {
            $query->where('fecha_compra', '>=', $fechaInicio);
        }
        
        if ($fechaFin) {
            $query->where('fecha_compra', '<=', $fechaFin);
        }
        
        return $query->orderBy('fecha_compra', 'desc')->get();
    }
    
    /**
     * Obtener estadísticas de compras
     */
    public function obtenerEstadisticasCompras($fechaInicio = null, $fechaFin = null)
    {
        $query = Compra::query();
        
        if ($fechaInicio) {
            $query->where('fecha_compra', '>=', $fechaInicio);
        }
        
        if ($fechaFin) {
            $query->where('fecha_compra', '<=', $fechaFin);
        }
        
        $compras = $query->get();
        
        return [
            'total_compras' => $compras->count(),
            'monto_total' => $compras->sum('total'),
            'promedio_compra' => $compras->count() > 0 ? $compras->avg('total') : 0,
            'compras_por_estado' => $compras->groupBy('estado')->map->count(),
            'compras_por_mes' => $compras->groupBy(function ($compra) {
                return $compra->fecha_compra->format('Y-m');
            })->map->count()
        ];
    }
    
    /**
     * Obtener ingredientes que necesitan reposición
     */
    public function obtenerIngredientesParaReposicion()
    {
        return Almacen::with('ingrediente')
                     ->stockBajo()
                     ->activos()
                     ->get()
                     ->map(function ($almacen) {
                         return [
                             'ingrediente' => $almacen->ingrediente->nombre,
                             'stock_actual' => $almacen->stock_actual,
                             'stock_minimo' => $almacen->stock_minimo,
                             'unidad_medida' => $almacen->unidad_medida,
                             'sugerido_comprar' => max(0, $almacen->stock_maximo - $almacen->stock_actual),
                             'ubicacion' => $almacen->ubicacion_fisica
                         ];
                     });
    }
}