<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AsignacionRolPermisoController extends Controller
{
    /**
     * Display a listing of users with their role assignments.
     */
    public function index()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        
        // Acceso: si es Administrador (rol) o tiene permiso 'usuarios' permite el acceso.
        // Se elimina el permiso inexistente 'Asignacion Roles y Permisos' y se usa el nuevo esquema.
        if (!$user->hasRole('Administrador') && !$user->hasPermissionTo('usuarios')) {
            return redirect()->to('admin/rol-error');
        }
        
        $users = User::with(['roles', 'permissions'])->where('estado', 1)->get();
        $roles = Role::all();
        $permissions = Permission::all();
        
        return view('admin.asignacion-roles-permisos.index', compact('users', 'roles', 'permissions'));
    }

    /**
     * Show the form for creating a new user assignment.
     */
    public function create()
    {
        $users = User::where('estado', 1)->get();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('admin.asignacion-roles-permisos.create', compact('users', 'roles', 'permissions'));
    }

    /**
     * Store a newly created user assignment.
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->user_id);
            
            // Asignar roles
            $user->syncRoles($request->roles ?? []);
            
            // Asignar permisos directos
            $user->syncPermissions($request->permissions ?? []);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Roles y permisos asignados correctamente al usuario.',
                'user' => $user->load(['roles', 'permissions'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar roles y permisos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($userId)
    {
        $user = User::with(['roles', 'permissions', 'persona'])->findOrFail($userId);
        $allPermissions = $user->getAllPermissions();
        $directPermissions = $user->getDirectPermissions();
        $rolePermissions = $allPermissions->diff($directPermissions);
        
        return view('admin.asignacion-roles-permisos.show', compact('user', 'allPermissions', 'directPermissions', 'rolePermissions'));
    }

    public function edit($userId)
    {
        $user = User::with(['roles', 'permissions'])->findOrFail($userId);
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('admin.asignacion-roles-permisos.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(\Illuminate\Http\Request $request, $userId)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'nullable|array', 
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);
            $user->syncRoles($request->roles ?? []);
            $user->syncPermissions($request->permissions ?? []);

            DB::commit();

            return redirect()->route('admin.asignacion-roles-permisos')
                           ->with('success', 'Asignaciones actualizadas correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar asignaciones: ' . $e->getMessage()]);
        }
    }

    public function destroy($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->syncRoles([]);
            $user->syncPermissions([]);

            return redirect()->route('admin.asignacion-roles-permisos')
                           ->with('success', 'Todas las asignaciones han sido removidas del usuario.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al remover asignaciones: ' . $e->getMessage()]);
        }
    }

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

    public function getRolesSpatie()
    {
        try {
            $roles = Role::with('permissions')->get();
            
            return response()->json([
                'success' => true,
                'roles' => $roles->map(function($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'permissions' => $role->permissions->pluck('name')->toArray()
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar roles'
            ], 500);
        }
    }

    public function getPermisosSpatie()
    {
        try {
            $permissions = Permission::all();
            
            return response()->json([
                'success' => true,
                'permissions' => $permissions->pluck('name')->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar permisos'
            ], 500);
        }
    }

    public function assignRole(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_name' => 'required|exists:roles,name'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $user->assignRole($request->role_name);

            return response()->json([
                'success' => true,
                'message' => 'Rol asignado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar rol: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeRole(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_name' => 'required|exists:roles,name'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $user->removeRole($request->role_name);

            return response()->json([
                'success' => true,
                'message' => 'Rol removido correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al remover rol: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignPermission(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_name' => 'required|exists:permissions,name'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $user->givePermissionTo($request->permission_name);

            return response()->json([
                'success' => true,
                'message' => 'Permiso asignado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar permiso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removePermission(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_name' => 'required|exists:permissions,name'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $user->revokePermissionTo($request->permission_name);

            return response()->json([
                'success' => true,
                'message' => 'Permiso removido correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al remover permiso: ' . $e->getMessage()
            ], 500);
        }
    }
}
