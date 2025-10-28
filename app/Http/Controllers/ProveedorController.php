<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proveedor::withCount('compras');
        
        // Filtros
        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('ruc', 'LIKE', "%{$buscar}%")
                  ->orWhere('email', 'LIKE', "%{$buscar}%")
                  ->orWhere('contacto', 'LIKE', "%{$buscar}%");
            });
        }
        
        if ($request->filled('estado')) {
            if ($request->get('estado') === 'activo') {
                $query->activos();
            } else {
                $query->inactivos();
            }
        }
        
        $proveedores = $query->orderBy('nombre')->paginate(15);
        
        // Estadísticas
        $estadisticas = [
            'total_proveedores' => Proveedor::count(),
            'activos' => Proveedor::activos()->count(),
            'inactivos' => Proveedor::inactivos()->count(),
            'con_compras' => Proveedor::has('compras')->count()
        ];
        
        return view('proveedores.index', compact('proveedores', 'estadisticas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:200',
            'ruc' => 'required|string|size:11|unique:proveedores,ruc',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:500',
            'contacto' => 'nullable|string|max:150',
            'estado' => 'required|in:activo,inactivo'
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.size' => 'El RUC debe tener exactamente 11 dígitos.',
            'ruc.unique' => 'Ya existe un proveedor con este RUC.',
            'email.email' => 'Debe ingresar un email válido.',
        ]);
        
        try {
            Proveedor::create($request->all());
            return redirect()->route('proveedores.index')
                           ->with('success', 'Proveedor creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear proveedor: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $proveedor = Proveedor::with(['compras' => function($query) {
                                     $query->latest()->take(10);
                                 }])->findOrFail($id);
        
        // Estadísticas del proveedor
        $estadisticas = [
            'total_compras' => $proveedor->compras()->count(),
            'compras_completadas' => $proveedor->compras()->completadas()->count(),
            'compras_pendientes' => $proveedor->compras()->pendientes()->count(),
            'monto_total' => $proveedor->compras()->completadas()->sum('total'),
            'ultima_compra' => $proveedor->compras()->latest()->first()?->fecha_compra,
            'promedio_compra' => $proveedor->compras()->completadas()->avg('total') ?? 0
        ];
                                 
        return view('proveedores.show', compact('proveedor', 'estadisticas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:200',
            'ruc' => ['required', 'string', 'size:11', Rule::unique('proveedores', 'ruc')->ignore($proveedor->id_proveedor, 'id_proveedor')],
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:500',
            'contacto' => 'nullable|string|max:150',
            'estado' => 'required|in:activo,inactivo'
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.size' => 'El RUC debe tener exactamente 11 dígitos.',
            'ruc.unique' => 'Ya existe un proveedor con este RUC.',
            'email.email' => 'Debe ingresar un email válido.',
        ]);
        
        try {
            $proveedor->update($request->all());
            return redirect()->route('proveedores.show', $proveedor->id_proveedor)
                           ->with('success', 'Proveedor actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar proveedor: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            
            // Verificar si tiene compras
            if ($proveedor->compras()->count() > 0) {
                return back()->withErrors(['error' => 'No se puede eliminar un proveedor que tiene compras registradas. Puede desactivarlo cambiando su estado.']);
            }
            
            $proveedor->delete();
            return redirect()->route('proveedores.index')
                           ->with('success', 'Proveedor eliminado exitosamente.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar proveedor: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Cambiar estado del proveedor
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            $nuevoEstado = $proveedor->estado === 'activo' ? 'inactivo' : 'activo';
            
            $proveedor->update(['estado' => $nuevoEstado]);
            
            $mensaje = $nuevoEstado === 'activo' 
                     ? 'Proveedor activado exitosamente.' 
                     : 'Proveedor desactivado exitosamente.';
                     
            return back()->with('success', $mensaje);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al cambiar estado: ' . $e->getMessage()]);
        }
    }
    
    /**
     * API para obtener proveedores (para select2)
     */
    public function getProveedores(Request $request)
    {
        $proveedores = Proveedor::activos()
                               ->when($request->get('search'), function($query, $search) {
                                   return $query->where(function($q) use ($search) {
                                       $q->where('nombre', 'LIKE', "%{$search}%")
                                         ->orWhere('ruc', 'LIKE', "%{$search}%");
                                   });
                               })
                               ->orderBy('nombre')
                               ->get()
                               ->map(function($proveedor) {
                                   return [
                                       'id' => $proveedor->id_proveedor,
                                       'text' => $proveedor->nombre_completo,
                                       'nombre' => $proveedor->nombre,
                                       'ruc' => $proveedor->ruc,
                                       'telefono' => $proveedor->telefono,
                                       'email' => $proveedor->email,
                                       'direccion' => $proveedor->direccion,
                                       'contacto' => $proveedor->contacto
                                   ];
                               });
                               
        return response()->json($proveedores);
    }
}