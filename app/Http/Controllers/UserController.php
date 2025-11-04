<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Repartidor;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    #WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        // Dar acceso si es Administrador o si tiene permiso 'usuarios'
        if (!$user->hasRole('Administrador') && !$user->hasPermissionTo('usuarios')) {
            return redirect()->to('admin/rol-error');
        }
        return view('pizzeria.user.index');
    }

    public function getBienvenido ()
    {
        return view('pizzeria.user.bienvenido');
    }

    /**
     * Obtener personas por tipo para el selector dinámico
     */
    public function getPersonasPorTipo(Request $request)
    {
        $tipo = $request->get('tipo');
        $personas = [];
        
        switch ($tipo) {
            case 'cliente':
                $personas = Cliente::where('estado', 1)
                    ->select('id_cliente as id')
                    ->get()
                    ->map(function($cliente) {
                        return [
                            'id' => $cliente->id,
                            'nombre_completo' => "Cliente #{$cliente->id}",
                            'tipo' => 'Cliente'
                        ];
                    });
                break;
                
            case 'empleado':
                $personas = Empleado::where('estado', 1)
                    ->select('id_empleado as id')
                    ->get()
                    ->map(function($empleado) {
                        return [
                            'id' => $empleado->id,
                            'nombre_completo' => "Empleado #{$empleado->id}",
                            'tipo' => 'Empleado'
                        ];
                    });
                break;
                
            case 'repartidor':
                $personas = Repartidor::where('estado', 1)
                    ->select('id_repartidor as id')
                    ->get()
                    ->map(function($repartidor) {
                        return [
                            'id' => $repartidor->id,
                            'nombre_completo' => "Repartidor #{$repartidor->id}",
                            'tipo' => 'Repartidor'
                        ];
                    });
                break;
                
            default:
                return response()->json([]);
        }
        
        return response()->json($personas);
    }

    #API REST
    public function index()
    {
        $data = User::where('estado', 1)
                    ->with('rol')
                    ->with('persona')
                    ->with('roles') // Cargar roles de Spatie
                    ->get();
        
        // Agregar información de roles de Spatie y persona con tipo
        $data->each(function($user) {
            $user->spatie_roles = $user->roles->pluck('name')->toArray();
            $user->all_permissions = $user->getAllPermissions()->pluck('name')->toArray();
            
            // Obtener información de la persona con su tipo
            $user->persona_info = $this->getPersonaInfo($user);
        });
        
        return new UserCollection($data);
    }

    private function getPersonaInfo($user)
    {
        if (!$user->id_persona || !$user->tipo_persona) {
            return 'Sin persona asignada';
        }
        
        switch($user->tipo_persona) {
            case 'cliente':
                $cliente = Cliente::where('id_cliente', $user->id_persona)->first();
                return $cliente ? "Cliente #{$cliente->id_cliente} — Cliente" : 'Cliente no encontrado';
                
            case 'empleado':
                $empleado = Empleado::where('id_empleado', $user->id_persona)->first();
                return $empleado ? "Empleado #{$empleado->id_empleado} — Empleado" : 'Empleado no encontrado';
                
            case 'repartidor':
                $repartidor = Repartidor::where('id_repartidor', $user->id_persona)->first();
                return $repartidor ? "Repartidor #{$repartidor->id_repartidor} — Repartidor" : 'Repartidor no encontrado';
                
            default:
                return 'Tipo de persona desconocido';
        }
    }

    public function inicioSesion(Request $request)
    {
        $response = [];
        $datos = $request->json()->all();

        $data = User::where('email', $datos['email'])
                    ->with('rol')
                    ->with('persona')
                    ->with('roles') // Incluir roles de Spatie
                    ->get();

        if (count($data) > 0) {
            $user = $data[0];
            if (Hash::check($datos['password'], $user['password'])) {
                // Agregar información de roles y permisos
                $user->spatie_roles = $user->roles->pluck('name')->toArray();
                $user->permissions = $user->getAllPermissions()->pluck('name')->toArray();
                $user->persona_info = $this->getPersonaInfo($user);
                
                $response = [
                    'message' => 'Sesión inicia correctamente.',
                    'status' => 200,
                    'data' => $user,
                ];
            } else {
                $response = [
                    'message' => 'Credenciales incorrectas.',
                    'status' => 501,
                    'data' => 0,
                ];
            }
        } else {
            $response = [
                'message' => 'Credenciales incorrectas.',
                'status' => 500,
                'data' => 0,
            ];
        }

        return response()->json($response);
    }

    /**
     * Método especial para registro de clientes desde el formulario web
     * Asigna automáticamente el rol "Cliente" por nombre
     */
    public function storeClienteWeb(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'id_persona' => 'required|integer',
        ]);

        $response = [];

        try {
            // Inicia una transacción
            DB::beginTransaction();
            
            $idPersona = (int)($request->get('id_persona'));
            
            // VALIDAR UNICIDAD: Una persona solo puede tener un usuario
            $usuarioExistente = User::where('tipo_persona', 'cliente')
                                   ->where('id_persona', $idPersona)
                                   ->where('estado', 1)
                                   ->first();
            
            if ($usuarioExistente) {
                throw new \Exception("Ya existe un usuario activo para este cliente. Usuario existente: {$usuarioExistente->name}");
            }
            
            // Validar que el cliente existe
            $clienteExiste = Cliente::where('id_cliente', $idPersona)->where('estado', 1)->exists();
            
            if (!$clienteExiste) {
                throw new \Exception("El cliente seleccionado no existe o está inactivo");
            }
            
            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'password_zentyal' => $request->get('password'),
                'id_rol' => 1, // Rol por defecto, los roles reales se asignan vía Spatie
                'id_persona' => $idPersona,
                'tipo_persona' => 'cliente',
            ]);

            // Asignar rol "Cliente" por nombre usando Spatie
            $rolCliente = Role::where('name', 'Cliente')->first();
            if ($rolCliente) {
                $user->assignRole($rolCliente);
            } else {
                // Si no existe el rol "Cliente", intentar crear uno básico
                $rolCliente = Role::create(['name' => 'Cliente']);
                $user->assignRole($rolCliente);
            }

            DB::commit();

            // Recargar el usuario con sus roles
            $user->load('roles', 'permissions');

            $response = [
                'message' => 'Usuario cliente creado correctamente.',
                'status' => 200,
                'data' => $user,
            ];
        } catch (QueryException | ModelNotFoundException $e) {
            DB::rollBack();
            $response = [
                'message' => 'Error al crear el usuario.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $response = [
                'message' => $e->getMessage(),
                'status' => 422,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function store(StoreUserRequest $request)
    {
        $response = [];

        try {
            // Inicia una transacción
            DB::beginTransaction();
            
            // Validar que id_persona existe y tipo_persona está definido
            $idPersona = (int)($request->get('id_persona'));
            $tipoPersona = $request->get('tipo_persona');
            
            if (!$idPersona || !$tipoPersona) {
                throw new \Exception('Debe seleccionar una persona válida y su tipo');
            }
            
            // VALIDAR UNICIDAD: Una persona solo puede tener un usuario
            $usuarioExistente = User::where('tipo_persona', $tipoPersona)
                                   ->where('id_persona', $idPersona)
                                   ->where('estado', 1)
                                   ->first();
            
            if ($usuarioExistente) {
                throw new \Exception("Ya existe un usuario activo para esta persona. Usuario existente: {$usuarioExistente->name}");
            }
            
            // Validar que la persona existe en la tabla correspondiente
            $personaExiste = false;
            switch($tipoPersona) {
                case 'cliente':
                    $personaExiste = Cliente::where('id_cliente', $idPersona)->where('estado', 1)->exists();
                    break;
                case 'empleado':
                    $personaExiste = Empleado::where('id_empleado', $idPersona)->where('estado', 1)->exists();
                    break;
                case 'repartidor':
                    $personaExiste = Repartidor::where('id_repartidor', $idPersona)->where('estado', 1)->exists();
                    break;
            }
            
            if (!$personaExiste) {
                throw new \Exception("La persona seleccionada no existe o está inactiva");
            }
            
            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'password_zentyal' => $request->get('password'),
                'id_rol' => 1, // Rol por defecto, los roles reales se asignan vía Spatie
                'id_persona' => $idPersona,
                'tipo_persona' => $tipoPersona,
            ]);

            $destinationPath = 'images/user/';
            $nombre_campo = 'profile_photo_path';
            $this->uploadImage($request, $user, $nombre_campo, $destinationPath);

            DB::commit();

            $response = [
                'message' => 'Usuario creado correctamente.',
                'status' => 200,
                'data' => $user,
            ];
        } catch (QueryException | ModelNotFoundException $e) {
            DB::rollBack();
            $response = [
                'message' => 'Error al insertar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $response = [
                'message' => $e->getMessage(),
                'status' => 422,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function show(User $user)
    {
        $user->load('rol', 'persona', 'roles', 'permissions');
        
        // Agregar información adicional de roles y permisos
        $user->spatie_roles = $user->roles->pluck('name')->toArray();
        $user->direct_permissions = $user->getDirectPermissions()->pluck('name')->toArray();
        $user->all_permissions = $user->getAllPermissions()->pluck('name')->toArray();
        $user->persona_info = $this->getPersonaInfo($user);
        
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $response = [];

        try {
            if (!$user) {
                $response = [
                    'message' => 'User no encontrado.',
                    'status' => 404,
                ];
            } else {
                DB::beginTransaction();
                
                // Validar que id_persona existe y tipo_persona está definido
                $idPersona = (int)($request->get('id_persona'));
                $tipoPersona = $request->get('tipo_persona');
                
                if (!$idPersona || !$tipoPersona) {
                    throw new \Exception('Debe seleccionar una persona válida y su tipo');
                }
                
                // VALIDAR UNICIDAD: Una persona solo puede tener un usuario (excluyendo el actual)
                $usuarioExistente = User::where('tipo_persona', $tipoPersona)
                                       ->where('id_persona', $idPersona)
                                       ->where('estado', 1)
                                       ->where('id', '!=', $user->id)
                                       ->first();
                
                if ($usuarioExistente) {
                    throw new \Exception("Ya existe otro usuario activo para esta persona. Usuario existente: {$usuarioExistente->name}");
                }
                
                // Validar que la persona existe en la tabla correspondiente
                $personaExiste = false;
                switch($tipoPersona) {
                    case 'cliente':
                        $personaExiste = Cliente::where('id_cliente', $idPersona)->where('estado', 1)->exists();
                        break;
                    case 'empleado':
                        $personaExiste = Empleado::where('id_empleado', $idPersona)->where('estado', 1)->exists();
                        break;
                    case 'repartidor':
                        $personaExiste = Repartidor::where('id_repartidor', $idPersona)->where('estado', 1)->exists();
                        break;
                }
                
                if (!$personaExiste) {
                    throw new \Exception("La persona seleccionada no existe o está inactiva");
                }

                // Preparar datos para actualizar
                $updateData = [
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                    'id_persona' => $idPersona,
                    'tipo_persona' => $tipoPersona,
                ];
                
                // Solo actualizar contraseña si se proporciona
                if ($request->filled('password') && !empty($request->get('password'))) {
                    $updateData['password'] = Hash::make($request->get('password'));
                    $updateData['password_zentyal'] = $request->get('password');
                }

                $user->update($updateData);

                //Falta eliminar la imagen anterior
                $destinationPath = 'images/user/';
                $nombre_campo = 'profile_photo_path';
                $this->uploadImage($request, $user, $nombre_campo, $destinationPath);

                DB::commit();

                $response = [
                    'message' => 'Usuario actualizado correctamente.',
                    'status' => 200,
                    'data' => $user,
                ];
            }
        } catch (QueryException | ModelNotFoundException $e) {
            DB::rollBack();
            $response = [
                'message' => 'Error al actualizar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $response = [
                'message' => $e->getMessage(),
                'status' => 422,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function destroy(User $user)
    {
        $response = [];
        try {

            $user->update(['estado' => 0]);
            $response = [
                'message' => 'Registro eliminado correctamente.',
                'status' => 200,
                'msg' => $user
            ];
        } catch (QueryException | ModelNotFoundException $e) {
            $response = [
                'message' => 'Error en la BD al eliminar el registro.',
                'status' => 500,
                'error' => $e
            ];
        } catch (\Exception $e) {
            $response = [
                'message' => 'Error general al eliminar el registro.',
                'status' => 500,
                'error' => $e
            ];
        }
        return json_encode($response);
    }

    public function eliminados()
    {
        $data = User::where('estado', 0)
                    ->with('rol')
                    ->with('persona')
                    ->with('roles')
                    ->get();
        
        // Agregar información de roles de Spatie a cada usuario
        $data->each(function($user) {
            $user->spatie_roles = $user->roles->pluck('name')->toArray();
            $user->persona_info = $this->getPersonaInfo($user);
        });
        
        return new UserCollection($data);
    }

    public function restaurar(User $user)
    {
        $response = [];
        try {
            $user->update(['estado' => 1]);

            $response = [
                'message' => 'Se restauró correctamente.',
                'status' => 200,
                'msg' => $user
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

    /**
     * Get users with their Spatie roles for API consumption.
     */
    public function getUsersWithRoles()
    {
        $users = User::where('estado', 1)
                     ->with(['roles', 'persona'])
                     ->get();
        
        $usersData = $users->map(function($user) {
            return [
                'id' => $user->id,
                'nombre' => $user->name,
                'correo' => $user->email,
                'rol_tradicional' => $user->rol ? $user->rol->nombre : null,
                'nombre_persona' => $this->getPersonaInfo($user),
                'roles_spatie' => $user->roles->pluck('name')->toArray(),
                'imagen' => $user->profile_photo_path,
                'permisos_totales' => $user->getAllPermissions()->count(),
                'created_at' => $user->created_at->format('Y-m-d H:i:s')
            ];
        });
        
        return response()->json([
            'usuarios' => $usersData,
            'total' => $users->count()
        ]);
    }

    public function uploadImage($request, $data, $imagen, $destinationPath)
    {
        if ($request->hasFile($imagen)) {
            $file = $request->file($imagen);
            $filename = time() . '-' . $data->getKey() . '.' . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move($destinationPath, $filename);

            if ($uploadSuccess) {
                $data->profile_photo_path = $destinationPath . $filename;
                $data->save(); // Guardar los cambios en el modelo
            }
        }
    }
}