<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Almacenes;
use App\Models\InventarioAlmacen;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AlmacenesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Almacenes::query();
        
        // Filtros
        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('descripcion', 'LIKE', "%{$buscar}%")
                  ->orWhere('ubicacion', 'LIKE', "%{$buscar}%")
                  ->orWhere('responsable', 'LIKE', "%{$buscar}%");
            });
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }
        
        $almacenes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Estadísticas
        $estadisticas = [
            'total_almacenes' => Almacenes::count(),
            'almacenes_activos' => Almacenes::activos()->count(),
            'almacenes_inactivos' => Almacenes::inactivos()->count(),
            'total_inventario' => InventarioAlmacen::activos()->count()
        ];
        
        return view('almacenes.index', compact('almacenes', 'estadisticas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('almacenes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:almacenes,nombre',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:255',
            'responsable' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:activo,inactivo'
        ]);
        
        try {
            $almacen = Almacenes::create($request->all());
            
            return redirect()->route('almacenes.index')
                           ->with('success', 'Almacén creado correctamente.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear almacén: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $almacen = Almacenes::findOrFail($id);
        
        // Inventario del almacén con paginación
        $inventario = InventarioAlmacen::where('id_almacen', $id)
                                      ->with('ingrediente')
                                      ->orderBy('stock_actual', 'asc')
                                      ->paginate(15);
        
        // Estadísticas del almacén
        $estadisticas = [
            'total_ingredientes' => $almacen->total_ingredientes,
            'productos_con_stock' => $almacen->productos_con_stock,
            'productos_stock_bajo' => $almacen->productos_stock_bajo,
            'valor_total_inventario' => $almacen->valor_total_inventario
        ];
        
        return view('almacenes.show', compact('almacen', 'inventario', 'estadisticas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $almacen = Almacenes::findOrFail($id);
        return view('almacenes.edit', compact('almacen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $almacen = Almacenes::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:100|unique:almacenes,nombre,' . $id . ',id_almacen',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:255',
            'responsable' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:activo,inactivo'
        ]);
        
        try {
            $almacen->update($request->all());
            
            return redirect()->route('almacenes.index')
                           ->with('success', 'Almacén actualizado correctamente.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar almacén: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $almacen = Almacenes::findOrFail($id);
            
            // Verificar si tiene inventario
            if ($almacen->tieneIngredientes()) {
                return back()->withErrors(['error' => 'No se puede eliminar un almacén que tiene inventario registrado.']);
            }
            
            // Verificar si tiene compras asociadas
            if ($almacen->compras()->count() > 0) {
                return back()->withErrors(['error' => 'No se puede eliminar un almacén que tiene compras registradas.']);
            }
            
            $almacen->delete();
            
            return redirect()->route('almacenes.index')
                           ->with('success', 'Almacén eliminado correctamente.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar almacén: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Obtener lista de almacenes para select (AJAX)
     */
    public function getAlmacenesActivos()
    {
        $almacenes = Almacenes::activos()
                             ->select('id_almacen', 'nombre', 'ubicacion')
                             ->orderBy('nombre')
                             ->get();
                             
        return response()->json($almacenes);
    }
    
    /**
     * Dashboard del almacén
     */
    public function dashboard()
    {
        $estadisticas = [
            'total_almacenes' => Almacenes::count(),
            'almacenes_activos' => Almacenes::activos()->count(),
            'total_ingredientes' => InventarioAlmacen::activos()->count(),
            'productos_stock_bajo' => InventarioAlmacen::stockBajo()->count(),
            'productos_agotados' => InventarioAlmacen::agotados()->count(),
            'valor_total_inventario' => InventarioAlmacen::activos()
                                                     ->sum(DB::raw('stock_actual * costo_unitario_promedio'))
        ];
        
        // Almacenes con más productos en stock bajo
        $almacenesStockBajo = Almacenes::activos()
                                      ->withCount(['inventario as stock_bajo_count' => function($query) {
                                          $query->whereRaw('stock_actual <= stock_minimo');
                                      }])
                                      ->having('stock_bajo_count', '>', 0)
                                      ->orderByDesc('stock_bajo_count')
                                      ->limit(5)
                                      ->get();
        
        // Productos próximos a vencer (en los próximos 7 días)
        $productosProximosVencer = InventarioAlmacen::proximosVencer(7)
                                                    ->with(['almacen', 'ingrediente'])
                                                    ->orderBy('fecha_vencimiento')
                                                    ->limit(10)
                                                    ->get();
        
        return view('almacenes.dashboard', compact(
            'estadisticas', 
            'almacenesStockBajo', 
            'productosProximosVencer'
        ));
    }
    
    /**
     * Reporte de stock bajo por almacén
     */
    public function reporteStockBajo(Request $request)
    {
        $query = InventarioAlmacen::stockBajo()
                                  ->with(['almacen', 'ingrediente'])
                                  ->activos();
        
        if ($request->filled('id_almacen')) {
            $query->where('id_almacen', $request->id_almacen);
        }
        
        $productosStockBajo = $query->orderBy('stock_actual')
                                   ->paginate(20);
        
        $almacenes = Almacenes::activos()->get();
        
        return view('almacenes.reporte-stock-bajo', compact('productosStockBajo', 'almacenes'));
    }
}