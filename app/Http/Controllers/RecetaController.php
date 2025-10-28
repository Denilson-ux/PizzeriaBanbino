<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemMenu;
use App\Models\Ingrediente;
use App\Models\Almacen;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    /**
     * Mostrar receta de un ItemMenu (pizza)
     */
    public function show($idItemMenu)
    {
        $itemMenu = ItemMenu::with(['recetas.ingrediente.almacen', 'tipoMenu'])->findOrFail($idItemMenu);
        $ingredientesDisponibles = Ingrediente::activos()->orderBy('categoria', 'asc')->orderBy('nombre')->get();
        
        return view('recetas.show', compact('itemMenu', 'ingredientesDisponibles'));
    }
    
    /**
     * Agregar ingrediente a la receta
     */
    public function agregarIngrediente(Request $request, $idItemMenu)
    {
        $request->validate([
            'id_ingrediente' => 'required|exists:ingredientes,id_ingrediente',
            'cantidad_necesaria' => 'required|numeric|min:0.001',
            'unidad_receta' => 'required|in:gramos,kilogramos,mililitros,litros,unidades,porciones',
            'observaciones' => 'nullable|string|max:200'
        ]);
        
        $itemMenu = ItemMenu::findOrFail($idItemMenu);
        $ingrediente = Ingrediente::findOrFail($request->id_ingrediente);
        
        // Verificar si ya existe en la receta
        if ($itemMenu->recetas()->where('id_ingrediente', $request->id_ingrediente)->exists()) {
            return back()->withErrors(['error' => 'Este ingrediente ya está en la receta. Edita la cantidad existente.']);
        }
        
        try {
            $itemMenu->recetas()->attach($request->id_ingrediente, [
                'cantidad_necesaria' => $request->cantidad_necesaria,
                'unidad_receta' => $request->unidad_receta,
                'observaciones' => $request->observaciones
            ]);
            
            return back()->with('success', 'Ingrediente agregado a la receta exitosamente.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al agregar ingrediente: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Actualizar ingrediente en la receta
     */
    public function actualizarIngrediente(Request $request, $idItemMenu, $idIngrediente)
    {
        $request->validate([
            'cantidad_necesaria' => 'required|numeric|min:0.001',
            'unidad_receta' => 'required|in:gramos,kilogramos,mililitros,litros,unidades,porciones',
            'observaciones' => 'nullable|string|max:200'
        ]);
        
        try {
            $itemMenu = ItemMenu::findOrFail($idItemMenu);
            
            $itemMenu->recetas()->updateExistingPivot($idIngrediente, [
                'cantidad_necesaria' => $request->cantidad_necesaria,
                'unidad_receta' => $request->unidad_receta,
                'observaciones' => $request->observaciones
            ]);
            
            return back()->with('success', 'Ingrediente actualizado en la receta.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar ingrediente: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Eliminar ingrediente de la receta
     */
    public function eliminarIngrediente($idItemMenu, $idIngrediente)
    {
        try {
            $itemMenu = ItemMenu::findOrFail($idItemMenu);
            $itemMenu->recetas()->detach($idIngrediente);
            
            return back()->with('success', 'Ingrediente eliminado de la receta.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar ingrediente: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Verificar disponibilidad de ingredientes para una cantidad de platos
     */
    public function verificarDisponibilidad(Request $request, $idItemMenu)
    {
        $request->validate([
            'cantidad_platos' => 'required|integer|min:1'
        ]);
        
        $itemMenu = ItemMenu::with('recetas.ingrediente.almacen')->findOrFail($idItemMenu);
        $cantidadPlatos = $request->cantidad_platos;
        
        $disponibilidad = [];
        $puedePreparar = true;
        
        foreach ($itemMenu->recetas as $receta) {
            $ingrediente = $receta->ingrediente;
            $cantidadNecesaria = $receta->pivot->cantidad_necesaria * $cantidadPlatos;
            $stockDisponible = $ingrediente->almacen ? $ingrediente->almacen->stock_actual : 0;
            $suficiente = $stockDisponible >= $cantidadNecesaria;
            
            if (!$suficiente) {
                $puedePreparar = false;
            }
            
            $disponibilidad[] = [
                'ingrediente' => $ingrediente->nombre,
                'necesario' => $cantidadNecesaria,
                'disponible' => $stockDisponible,
                'suficiente' => $suficiente,
                'faltante' => $suficiente ? 0 : ($cantidadNecesaria - $stockDisponible),
                'unidad' => $receta->pivot->unidad_receta
            ];
        }
        
        return response()->json([
            'puede_preparar' => $puedePreparar,
            'cantidad_maxima' => $this->calcularCantidadMaxima($itemMenu),
            'ingredientes' => $disponibilidad
        ]);
    }
    
    /**
     * Calcular cuántos platos se pueden preparar como máximo
     */
    private function calcularCantidadMaxima($itemMenu)
    {
        $cantidadMaxima = PHP_INT_MAX;
        
        foreach ($itemMenu->recetas as $receta) {
            $ingrediente = $receta->ingrediente;
            $stockDisponible = $ingrediente->almacen ? $ingrediente->almacen->stock_actual : 0;
            $cantidadPorPlato = $receta->pivot->cantidad_necesaria;
            
            if ($cantidadPorPlato > 0) {
                $platosDisponibles = floor($stockDisponible / $cantidadPorPlato);
                $cantidadMaxima = min($cantidadMaxima, $platosDisponibles);
            }
        }
        
        return $cantidadMaxima === PHP_INT_MAX ? 0 : $cantidadMaxima;
    }
    
    /**
     * Descontar ingredientes del almacén al realizar una venta
     */
    public static function descontarIngredientesPorVenta($idItemMenu, $cantidad, $idUsuario = null, $referencia = null)
    {
        $itemMenu = ItemMenu::with('recetas.ingrediente.almacen')->findOrFail($idItemMenu);
        $movimientos = [];
        
        try {
            DB::beginTransaction();
            
            foreach ($itemMenu->recetas as $receta) {
                $ingrediente = $receta->ingrediente;
                $cantidadNecesaria = $receta->pivot->cantidad_necesaria * $cantidad;
                
                if ($ingrediente->almacen) {
                    $almacen = $ingrediente->almacen;
                    
                    if ($almacen->stock_actual >= $cantidadNecesaria) {
                        $stockAnterior = $almacen->stock_actual;
                        $nuevoStock = $almacen->stock_actual - $cantidadNecesaria;
                        
                        $almacen->update([
                            'stock_actual' => $nuevoStock,
                            'fecha_ultimo_egreso' => now()
                        ]);
                        
                        // Crear movimiento
                        $movimiento = $almacen->movimientos()->create([
                            'tipo_movimiento' => 'salida',
                            'cantidad' => $cantidadNecesaria,
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo' => $nuevoStock,
                            'motivo' => 'venta',
                            'fecha_movimiento' => now(),
                            'usuario_id' => $idUsuario ?: auth()->id(),
                            'referencia_id' => $referencia,
                            'referencia_tipo' => 'venta',
                            'observaciones' => "Venta de {$cantidad}x {$itemMenu->nombre}"
                        ]);
                        
                        $movimientos[] = $movimiento;
                    } else {
                        throw new \Exception("Stock insuficiente del ingrediente: {$ingrediente->nombre}");
                    }
                }
            }
            
            DB::commit();
            return ['success' => true, 'movimientos' => $movimientos];
            
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}