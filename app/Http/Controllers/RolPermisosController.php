<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RolPermisosController extends Controller
{
    /**
     * Display a listing of roles with their permissions.
     */
    public function index()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!($user->hasPermissionTo('usuarios'))) {
            return redirect()->to('admin/rol-error');
        }
        
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('pizzeria.rol-permisos.index', compact('roles', 'permissions'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('pizzeria.rol-permisos.create', compact('permissions'));
    }

    /**
     * Store a newly created role with permissions.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            DB::beginTransaction();

            // Crear el rol
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            // Asignar permisos al rol si se seleccionaron
            if ($request->has('permissions') && is_array($request->permissions)) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();

            return response()->json([
                'message' => 'Rol creado correctamente con sus permisos.',
                'status' => 200,
                'role' => $role->load('permissions')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el rol: ' . $e->getMessage(),
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified role with its permissions.
     */
    public function show($id)
    {
        $role = Role::with('permissions', 'users')->findOrFail($id);
        return response()->json([
            'role' => $role,
            'users_count' => $role->users->count(),
            'permissions_count' => $role->permissions->count()
        ]);
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        
        return view('pizzeria.rol-permisos.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role and its permissions.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            DB::beginTransaction();

            $role = Role::findOrFail($id);
            
            // Actualizar el nombre del rol
            $role->update([
                'name' => $request->name
            ]);

            // Sincronizar permisos
            if ($request->has('permissions') && is_array($request->permissions)) {
                $role->syncPermissions($request->permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Rol actualizado correctamente.',
                'status' => 200,
                'role' => $role->load('permissions')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el rol.',
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            
            // Verificar si el rol tiene usuarios asignados
            if ($role->users()->count() > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar el rol porque tiene usuarios asignados.',
                    'status' => 400
                ]);
            }

            $role->delete();

            return response()->json([
                'message' => 'Rol eliminado correctamente.',
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el rol.',
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get all roles for API.
     */
    public function getRoles()
    {
        $roles = Role::with('permissions')->get();
        
        return response()->json([
            'roles' => $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'nombre' => $role->name,
                    'permisos' => $role->permissions->pluck('name'),
                    'usuarios_count' => $role->users()->count(),
                    'permisos_count' => $role->permissions->count(),
                    'created_at' => $role->created_at ? $role->created_at->format('Y-m-d H:i:s') : null
                ];
            })
        ]);
    }

    /**
     * Get all roles for Spatie system.
     */
    public function getRolesSpatie()
    {
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
    }

    /**
     * Get all permissions for Spatie system.
     */
    public function getPermisosSpatie()
    {
        $permissions = Permission::all();
        
        return response()->json([
            'success' => true,
            'data' => $permissions->pluck('name')->toArray()
        ]);
    }

    /**
     * Store a new role via API.
     */
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
                $validPermissions = Permission::whereIn('name', $request->permissions)->pluck('name')->toArray();
                $role->syncPermissions($validPermissions);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rol creado exitosamente.',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->toArray()
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
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

    /**
     * Update an existing role via API.
     */
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
                $validPermissions = Permission::whereIn('name', $request->permissions)->pluck('name')->toArray();
                $role->syncPermissions($validPermissions);
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
                    'permissions' => $role->permissions->pluck('name')->toArray()
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
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

    /**
     * Delete a role via API.
     */
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

    /**
     * Get permissions for a specific role.
     */
    public function getRolePermissions($roleId)
    {
        try {
            $role = Role::with('permissions')->findOrFail($roleId);
            
            return response()->json([
                'success' => true,
                'permissions' => $role->permissions->pluck('name')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        }
    }

    /**
     * Assign permission to role.
     */
    public function assignPermission(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission' => 'required|exists:permissions,name'
        ]);

        try {
            $role = Role::findOrFail($request->role_id);
            $role->givePermissionTo($request->permission);

            return response()->json([
                'success' => true,
                'message' => 'Permiso asignado correctamente al rol.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar permiso.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove permission from role.
     */
    public function removePermission(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission' => 'required|exists:permissions,name'
        ]);

        try {
            $role = Role::findOrFail($request->role_id);
            $role->revokePermissionTo($request->permission);

            return response()->json([
                'success' => true,
                'message' => 'Permiso removido correctamente del rol.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al remover permiso.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}