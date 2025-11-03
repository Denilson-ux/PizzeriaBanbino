<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Almacenes;
use App\Models\InventarioAlmacen;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AlmacenesController extends Controller
{
    public function index(Request $request)
    {
        $query = Almacenes::query();
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
        $estadisticas = [
            'total_almacenes' => Almacenes::count(),
            'almacenes_activos' => Almacenes::activos()->count(),
            'almacenes_inactivos' => Almacenes::inactivos()->count(),
            'total_inventario' => InventarioAlmacen::activos()->count()
        ];
        return view('almacenes.index', compact('almacenes', 'estadisticas'));
    }

    public function create()
    {
        return view('almacenes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:almacenes_fisicos,nombre',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:255',
            'responsable' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:activo,inactivo'
        ]);
        try {
            $almacen = Almacenes::create($request->all());
            return redirect()->route('almacenes.index')->with('success', 'Almacén creado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear almacén: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $almacen = Almacenes::findOrFail($id);
        $inventario = InventarioAlmacen::where('id_almacen', $id)->with('ingrediente')->orderBy('stock_actual', 'asc')->paginate(15);
        $estadisticas = [
            'total_ingredientes' => $almacen->total_ingredientes,
            'productos_con_stock' => $almacen->productos_con_stock,
            'productos_stock_bajo' => $almacen->productos_stock_bajo,
            'valor_total_inventario' => $almacen->valor_total_inventario
        ];
        return view('almacenes.show', compact('almacen', 'inventario', 'estadisticas'));
    }

    public function edit(string $id)
    {
        $almacen = Almacenes::findOrFail($id);
        return view('almacenes.edit', compact('almacen'));
    }

    public function update(Request $request, string $id)
    {
        $almacen = Almacenes::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:100|unique:almacenes_fisicos,nombre,' . $id . ',id_almacen',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:255',
            'responsable' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:activo,inactivo'
        ]);
        try {
            $almacen->update($request->all());
            return redirect()->route('almacenes.index')->with('success', 'Almacén actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar almacén: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $almacen = Almacenes::findOrFail($id);
            if ($almacen->tieneIngredientes()) {
                return back()->withErrors(['error' => 'No se puede eliminar un almacén que tiene inventario registrado.']);
            }
            if ($almacen->compras()->count() > 0) {
                return back()->withErrors(['error' => 'No se puede eliminar un almacén que tiene compras registradas.']);
            }
            $almacen->delete();
            return redirect()->route('almacenes.index')->with('success', 'Almacén eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar almacén: ' . $e->getMessage()]);
        }
    }

    public function getAlmacenesActivos()
    {
        $almacenes = Almacenes::activos()->select('id_almacen', 'nombre', 'ubicacion')->orderBy('nombre')->get();
        return response()->json($almacenes);
    }
}
