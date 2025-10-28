<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Http\Requests\StoreRolRequest;
use App\Http\Requests\UpdateRolRequest;
use App\Http\Resources\RolCollection;
use App\Http\Resources\RolResource;
use App\Models\Empleado;
use App\Models\Repartidor;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Rules\Role as RulesRole;

class RolController extends Controller
{
    #WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!($user->hasPermissionTo('usuarios'))) {
            return redirect()->to('admin/rol-error');
        };
        
        // Cargar roles de Spatie y permisos para la vista
        $rolesSpatie = Role::with('permissions')->get();
        $permissionsSpatie = Permission::all();
        
        return view('pizzeria.rol.index', compact('rolesSpatie', 'permissionsSpatie'));
    }

    public function getError()
    {
        return view('pizzeria.rol.error_rol');
    }

    #API REST para roles de Spatie
    public function getRolesSpatie()
    {
        try {
            $roles = Role::with('permissions')->get();
            
            return response()->json([
                'success' => true,
                'data' => $roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'guard_name' => $role->guard_name,
                        'permissions' => $role->permissions->pluck('name')->toArray(),
                        'permissions_count' => $role->permissions->count(),
                        'users_count' => $role->users()->count(),
                        'created_at' => $role->created_at ? $role->created_at->format('Y-m-d H:i:s') : null,
                        'updated_at' => $role->updated_at ? $role->updated_at->format('Y-m-d H:i:s') : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar roles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPermisosSpatie()
    {
        try {
            $permissions = Permission::all();
            
            return response()->json([
                'success' => true,
                'data' => $permissions->pluck('name')->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar permisos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeRoleSpatie(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string'
            ]);

            DB::beginTransaction();

            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            if ($request->has('permissions') && is_array($request->permissions)) {
                // Verificar que los permisos existan
                $existingPermissions = Permission::whereIn('name', $request->permissions)->pluck('name')->toArray();
                $role->syncPermissions($existingPermissions);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rol creado exitosamente.',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                    'permissions_count' => $role->permissions->count(),
                    'users_count' => 0
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRoleSpatie(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $id,
                'permissions' => 'nullable|array',
                'permissions.*' => 'string'
            ]);

            DB::beginTransaction();

            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->save();

            if ($request->has('permissions') && is_array($request->permissions)) {
                $existingPermissions = Permission::whereIn('name', $request->permissions)->pluck('name')->toArray();
                $role->syncPermissions($existingPermissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado exitosamente.',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                    'permissions_count' => $role->permissions->count(),
                    'users_count' => $role->users()->count()
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyRoleSpatie($id)
    {
        try {
            $role = Role::findOrFail($id);
            
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el rol porque tiene usuarios asignados.'
                ], 400);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showRoleSpatie($id)
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                    'permissions_count' => $role->permissions->count(),
                    'users_count' => $role->users()->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], 404);
        }
    }

    #API REST tradicional (mantenido para compatibilidad)
    public function index()
    {
        $roles = Rol::where('estado', 1);
        return new RolCollection($roles->get());
    }

    public function store(StoreRolRequest $request)
    {
        $response = [];
        try {

            $rol = Rol::create($request->all());
            $newRol = new RolResource($rol);
            $response = [
                'message' => 'Registro insertado correctamente.',
                'status' => 200,
                'msg' => $newRol
            ];
        } catch (QueryException | ModelNotFoundException $e) {
            $response = [
                'message' => 'Error al insertar el registro.',
                'status' => 500,
                'error' => $e
            ];
        } catch (\Exception $e) {
            $response = [
                'message' => 'Error general al insertar el registro.',
                'status' => 500,
                'error' => $e
            ];
        }
        return response()->json($response);
    }

    public function show(Rol $rol)
    {
        return new RolResource($rol);
    }

    public function update(UpdateRolRequest $request, Rol $rol)
    {
        $success = $rol->update($request->all());
        $response = [];
        if ($success) {
            $response = [
                'message' => 'La actualización fue exitosa',
                'status' => 200,
                'msg' => $rol
            ];
        } else {
            $response = [
                'message' => 'La actualización falló',
                'status' => 500
            ];
        }
        return response()->json($response);
    }

    public function destroy(Rol $rol)
    {
        $response = [];
        try {
            $rol->update(['estado' => 0]);

            $response = [
                'message' => 'Se eliminó correctamente.',
                'status' => 200,
                'msg' => $rol
            ];
        } catch (\Exception $e) {
            $response = [
                'message' => 'La error al eliminar',
                'status' => 500,
                'error' => $e
            ];
        }
        return response()->json($response);
    }

    public function eliminados()
    {
        $rolEliminados = Rol::where('estado', 0);
        return new RolCollection($rolEliminados->get());
    }

    public function restaurar(Rol $rol)
    {
        $response = [];
        try {
            $rol->update(['estado' => 1]);

            $response = [
                'message' => 'Se restauró correctamente.',
                'status' => 200,
                'msg' => $rol
            ];
        } catch (\Exception $e) {
            $response = [
                'message' => 'La error al resturar.',
                'status' => 500,
                'error' => $e
            ];
        }
        return response()->json($response);
    }

    public function asignarRoles()
    {
        $response = [];
        try {
            $admins = User::where('id_rol', 1)->get();
            $empleados = User::where('id_rol', 2)->get();
            $repartidores = User::where('id_rol', 3)->get();

            foreach ($admins as $admin) {
                $admin->assignRole('Administrador');
            }

            foreach ($empleados as $empleado) {
                $empleado->assignRole('Cajero');
            }

            foreach ($repartidores as $repartidor) {
                $repartidor->assignRole('Repartidor');
            }

            $response = [
                'message' => 'Roles asignados correctamente.',
                'status' => 200,
                'msg' => 0
            ];
        } catch (\Exception $e) {
            $response = [
                'message' => 'La error al asignar roles.',
                'status' => 500,
                'error' => $e
            ];
        }
        return response()->json($response);
    }
}