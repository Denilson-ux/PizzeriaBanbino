<?php

namespace App\Http\Controllers;

use App\Models\Ingrediente;
use Illuminate\Http\Request;

class IngredienteController extends Controller
{
    public function index()
    {
        $ingredientes = Ingrediente::orderBy('nombre')->paginate(20);
        return view('ingredientes.index', compact('ingredientes'));
    }

    public function create()
    {
        return view('ingredientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:200|unique:ingredientes,nombre',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|in:gramos,kilogramos,mililitros,litros,unidades,porciones',
            'categoria' => 'required|in:lacteos,carnes,vegetales,harinas,condimentos,bebidas,otros',
            'es_perecedero' => 'boolean',
            'dias_vencimiento' => 'nullable|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
        ]);

        Ingrediente::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'unidad_medida' => $validated['unidad_medida'],
            'categoria' => $validated['categoria'],
            'es_perecedero' => $validated['es_perecedero'] ?? false,
            'dias_vencimiento' => $validated['dias_vencimiento'] ?? null,
            'estado' => $validated['estado']
        ]);

        return redirect()->route('ingredientes.index')->with('success', 'Ingrediente creado exitosamente.');
    }

    public function show(Ingrediente $ingrediente)
    {
        // Sólo cargar relación de almacén si existe, y no consultar movimientos
        $ingrediente->load('almacen', 'itemsMenu');
        return view('ingredientes.show', compact('ingrediente'));
    }

    public function edit(Ingrediente $ingrediente)
    {
        return view('ingredientes.edit', compact('ingrediente'));
    }

    public function update(Request $request, Ingrediente $ingrediente)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:200|unique:ingredientes,nombre,' . $ingrediente->id_ingrediente . ',id_ingrediente',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|in:gramos,kilogramos,mililitros,litros,unidades,porciones',
            'categoria' => 'required|in:lacteos,carnes,vegetales,harinas,condimentos,bebidas,otros',
            'es_perecedero' => 'boolean',
            'dias_vencimiento' => 'nullable|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $ingrediente->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'unidad_medida' => $validated['unidad_medida'],
            'categoria' => $validated['categoria'],
            'es_perecedero' => $validated['es_perecedero'] ?? false,
            'dias_vencimiento' => $validated['dias_vencimiento'] ?? null,
            'estado' => $validated['estado']
        ]);

        return redirect()->route('ingredientes.index')->with('success', 'Ingrediente actualizado exitosamente.');
    }

    public function destroy(Ingrediente $ingrediente)
    {
        if ($ingrediente->itemsMenu()->count() > 0) {
            return redirect()->route('ingredientes.index')->with('error', 'No se puede eliminar el ingrediente porque está siendo usado en recetas.');
        }
        $ingrediente->delete();
        return redirect()->route('ingredientes.index')->with('success', 'Ingrediente eliminado exitosamente.');
    }

    public function buscar(Request $request)
    {
        $query = $request->get('q', '');
        $ingredientes = Ingrediente::where('nombre', 'LIKE', "%{$query}%")
                                 ->where('estado', 'activo')
                                 ->limit(10)
                                 ->get()
                                 ->map(function ($ingrediente) {
                                     return [
                                         'id' => $ingrediente->id_ingrediente,
                                         'nombre' => $ingrediente->nombre,
                                         'unidad_medida' => $ingrediente->unidad_medida,
                                         'stock_actual' => optional($ingrediente->almacen)->stock_actual ?? 0,
                                         'categoria' => $ingrediente->categoria
                                     ];
                                 });
        return response()->json($ingredientes);
    }

    public function infoStock($id)
    {
        $ingrediente = Ingrediente::with('almacen')->find($id);
        if (!$ingrediente) {
            return response()->json(['error' => 'Ingrediente no encontrado'], 404);
        }
        return response()->json([
            'id' => $ingrediente->id_ingrediente,
            'nombre' => $ingrediente->nombre,
            'stock_actual' => optional($ingrediente->almacen)->stock_actual ?? 0,
            'unidad_medida' => $ingrediente->unidad_medida,
        ]);
    }
}
