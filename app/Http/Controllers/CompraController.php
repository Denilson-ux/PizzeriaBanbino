<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Ingrediente;
use App\Models\Almacenes;
use App\Models\InventarioAlmacen;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'usuario', 'almacenDestino', 'detalles']);
        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('numero_compra', 'LIKE', "%{$buscar}%")
                  ->orWhere('numero_factura', 'LIKE', "%{$buscar}%")
                  ->orWhereHas('proveedor', function($prov) use ($buscar) {
                      $prov->where('nombre', 'LIKE', "%{$buscar}%")
                           ->orWhere('ruc', 'LIKE', "%{$buscar}%");
                  });
            });
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }
        if ($request->filled('proveedor_id')) {
            $query->where('id_proveedor', $request->get('proveedor_id'));
        }
        if ($request->filled('almacen_id')) {
            $query->where('id_almacen_destino', $request->get('almacen_id'));
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_compra', '>=', $request->get('fecha_inicio'));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_compra', '<=', $request->get('fecha_fin'));
        }
        $compras = $query->orderBy('created_at', 'desc')->paginate(15);
        $proveedores = Proveedor::activos()->orderBy('nombre')->get();
        $almacenes = Almacenes::activos()->orderBy('nombre')->get();
        $estadisticas = [
            'total_compras' => Compra::count(),
            'pendientes' => Compra::pendientes()->count(),
            'completadas' => Compra::completadas()->count(),
            'total_monto' => Compra::completadas()->sum('total'),
            'compras_mes' => Compra::delMes()->sum('total')
        ];
        return view('compras.index', compact('compras', 'proveedores', 'almacenes', 'estadisticas'));
    }

    public function create()
    {
        $proveedores = Proveedor::activos()->orderBy('nombre')->get();
        $almacenes = Almacenes::activos()->orderBy('nombre')->get();
        $ingredientes = Ingrediente::activos()->orderBy('categoria')->orderBy('nombre')->get();
        return view('compras.create', compact('proveedores', 'almacenes', 'ingredientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'id_almacen_destino' => 'required|exists:almacenes_fisicos,id_almacen',
            'fecha_compra' => 'required|date',
            'fecha_entrega' => 'nullable|date|after_or_equal:fecha_compra',
            'tipo_compra' => 'required|in:contado,credito',
            'numero_factura' => 'nullable|string|max:50',
            'aplicar_almacen' => 'boolean',
            'observaciones' => 'nullable|string|max:500',
            'ingredientes' => 'required|array|min:1',
            'ingredientes.*.id_ingrediente' => 'required|exists:ingredientes,id_ingrediente',
            'ingredientes.*.cantidad' => 'required|numeric|min:0.01',
            'ingredientes.*.precio_unitario' => 'required|numeric|min:0',
            'ingredientes.*.observaciones' => 'nullable|string|max:200'
        ]);

        try {
            DB::beginTransaction();
            $compra = Compra::create([
                'numero_compra' => Compra::generarNumeroCompra(),
                'id_proveedor' => $request->id_proveedor,
                'id_almacen_destino' => $request->id_almacen_destino,
                'id_usuario' => auth()->id(),
                'fecha_compra' => $request->fecha_compra,
                'fecha_entrega' => $request->fecha_entrega,
                'tipo_compra' => $request->tipo_compra,
                'numero_factura' => $request->numero_factura,
                'aplicar_almacen' => $request->boolean('aplicar_almacen', true),
                'observaciones' => $request->observaciones,
                'estado' => 'pendiente'
            ]);
            foreach ($request->ingredientes as $ingrediente) {
                DetalleCompra::create([
                    'id_compra' => $compra->id_compra,
                    'id_ingrediente' => $ingrediente['id_ingrediente'],
                    'cantidad' => $ingrediente['cantidad'],
                    'precio_unitario' => $ingrediente['precio_unitario'],
                    'observaciones' => $ingrediente['observaciones'] ?? null
                ]);
            }
            $compra->calcularTotales();
            DB::commit();
            return redirect()->route('compras.show', $compra->id_compra)->with('success', 'Compra de ingredientes creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al crear compra: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $compra = Compra::with(['proveedor', 'usuario', 'almacenDestino', 'detalles.ingrediente'])->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    public function edit(string $id)
    {
        $compra = Compra::with(['detalles.ingrediente', 'almacenDestino'])->findOrFail($id);
        if ($compra->estado !== 'pendiente') {
            return redirect()->route('compras.show', $id)->withErrors(['error' => 'Solo se pueden editar compras pendientes.']);
        }
        $proveedores = Proveedor::activos()->orderBy('nombre')->get();
        $almacenes = Almacenes::activos()->orderBy('nombre')->get();
        $ingredientes = Ingrediente::activos()->orderBy('categoria')->orderBy('nombre')->get();
        return view('compras.edit', compact('compra', 'proveedores', 'almacenes', 'ingredientes'));
    }

    public function update(Request $request, string $id)
    {
        $compra = Compra::findOrFail($id);
        if ($compra->estado !== 'pendiente') {
            return back()->withErrors(['error' => 'Solo se pueden editar compras pendientes.']);
        }
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'id_almacen_destino' => 'required|exists:almacenes_fisicos,id_almacen',
            'fecha_compra' => 'required|date',
            'fecha_entrega' => 'nullable|date|after_or_equal:fecha_compra',
            'tipo_compra' => 'required|in:contado,credito',
            'numero_factura' => 'nullable|string|max:50',
            'aplicar_almacen' => 'boolean',
            'observaciones' => 'nullable|string|max:500',
            'ingredientes' => 'required|array|min:1',
            'ingredientes.*.id_ingrediente' => 'required|exists:ingredientes,id_ingrediente',
            'ingredientes.*.cantidad' => 'required|numeric|min:0.01',
            'ingredientes.*.precio_unitario' => 'required|numeric|min:0',
            'ingredientes.*.observaciones' => 'nullable|string|max:200'
        ]);

        try {
            DB::beginTransaction();
            $compra->update([
                'id_proveedor' => $request->id_proveedor,
                'id_almacen_destino' => $request->id_almacen_destino,
                'fecha_compra' => $request->fecha_compra,
                'fecha_entrega' => $request->fecha_entrega,
                'tipo_compra' => $request->tipo_compra,
                'numero_factura' => $request->numero_factura,
                'aplicar_almacen' => $request->boolean('aplicar_almacen', true),
                'observaciones' => $request->observaciones
            ]);
            $compra->detalles()->delete();
            foreach ($request->ingredientes as $ingrediente) {
                DetalleCompra::create([
                    'id_compra' => $compra->id_compra,
                    'id_ingrediente' => $ingrediente['id_ingrediente'],
                    'cantidad' => $ingrediente['cantidad'],
                    'precio_unitario' => $ingrediente['precio_unitario'],
                    'observaciones' => $ingrediente['observaciones'] ?? null
                ]);
            }
            $compra->calcularTotales();
            DB::commit();
            return redirect()->route('compras.show', $compra->id_compra)->with('success', 'Compra actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al actualizar compra: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $compra = Compra::findOrFail($id);
            if ($compra->estado === 'completada') {
                return back()->withErrors(['error' => 'No se pueden eliminar compras completadas.']);
            }
            $compra->delete();
            return redirect()->route('compras.index')->with('success', 'Compra eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar compra: ' . $e->getMessage()]);
        }
    }

    public function completar(Request $request, $id)
    {
        try {
            $compra = Compra::findOrFail($id);
            $resultado = $compra->completarCompra();
            if ($resultado['success']) {
                return back()->with('success', $resultado['mensaje']);
            } else {
                return back()->withErrors(['error' => $resultado['mensaje']]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al completar compra: ' . $e->getMessage()]);
        }
    }

    public function cancelar(Request $request, $id)
    {
        try {
            $compra = Compra::findOrFail($id);
            if ($compra->estado === 'completada') {
                return back()->withErrors(['error' => 'No se pueden cancelar compras completadas.']);
            }
            $compra->update([
                'estado' => 'cancelada',
                'observaciones' => ($compra->observaciones ?? '') . "\nCancelada el " . now()->format('d/m/Y H:i') . " por " . auth()->user()->name
            ]);
            return back()->with('success', 'Compra cancelada exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al cancelar compra: ' . $e->getMessage()]);
        }
    }

    public function getIngredientes(Request $request)
    {
        $ingredientes = Ingrediente::activos()->when($request->get('search'), function($query, $search) {
                return $query->where('nombre', 'LIKE', "%{$search}%")->orWhere('categoria', 'LIKE', "%{$search}%");
            })->orderBy('categoria')->orderBy('nombre')->get()->map(function($ingrediente) {
                return [
                    'id' => $ingrediente->id_ingrediente,
                    'nombre' => $ingrediente->nombre,
                    'categoria' => $ingrediente->categoria ?? 'Otros',
                    'unidad_medida' => $ingrediente->unidad_medida,
                    'descripcion' => $ingrediente->descripcion
                ];
            });
        return response()->json($ingredientes);
    }

    public function getAlmacenes(Request $request)
    {
        $almacenes = Almacenes::activos()->select('id_almacen', 'nombre', 'ubicacion', 'responsable')->orderBy('nombre')->get();
        return response()->json($almacenes);
    }
}
