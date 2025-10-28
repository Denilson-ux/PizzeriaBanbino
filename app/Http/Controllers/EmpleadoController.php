<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Http\Requests\StoreEmpleadoRequest;
use App\Http\Requests\UpdateEmpleadoRequest;
use App\Http\Resources\EmpleadoCollection;
use App\Http\Resources\EmpleadoResource;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmpleadoController extends Controller
{
    
    #WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!($user->hasPermissionTo('usuarios'))) {
            return redirect()->to('admin/rol-error');
        };
        return view('pizzeria.empleado.index');
    }

    #API REST
    public function index()
    {
        $empleados = Empleado::where('estado', 1)->get();
        $empleadoPersona = [];
        $i = 0;
        foreach ($empleados as $empleado) {
            $empleadoPersona[$i]['id_empleado'] = $empleado['id_empleado'];
            $empleadoPersona[$i]['sueldo'] = $empleado['sueldo'];
            $empleadoPersona[$i]['estado'] = $empleado['estado'];
            $empleadoPersona[$i]['create_at'] = $empleado['create_at'];
            $empleadoPersona[$i]['update_at'] = $empleado['update_at'];
            $empleadoPersona[$i]['persona'] = Persona::findOrFail($empleado['id_empleado']);
            $i++;
        }

        // Para que retorne los datos y se vea mas pro :v                        
        $data = [
            'data' => $empleadoPersona
        ];
        return response()->json($data);
    }

    public function store(StoreEmpleadoRequest $request)
    {
        $response = [];

        try {
            DB::beginTransaction();

            $dataPerson = Persona::create([
                'nombre' => $request->get('nombre'),
                'paterno' => $request->get('paterno'),
                'materno' => $request->get('materno'),
                'telefono' => $request->get('telefono'),
                'direccion' => $request->get('direccion'),
                'correo' => $request->get('correo'),
            ]);

            // Subir la imagen
            $destinationPath = 'images/persona/empleado/';
            $nombre_campo = 'imagen';
            $this->uploadImage($request, $dataPerson, $nombre_campo, $destinationPath);

            $idPerson = $dataPerson->id_persona;

            $data = Empleado::create([
                'id_empleado' => $idPerson,
                'sueldo' => $request->get('sueldo')
            ]);

            DB::commit();

            $response = [
                'message' => 'Registro insertado correctamente.',
                'status' => 200,
                'data' => $idPerson,
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
                'message' => 'Error general al insertar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function show(Empleado $empleado)
    {
        $empleado['persona'] = Persona::findOrFail($empleado['id_empleado']);
        return new EmpleadoResource($empleado);
    }


    public function update(UpdateEmpleadoRequest $request, Empleado $empleado)
    {
        $response = [];

        try {
            if (!$empleado) {
                $response = [
                    'message' => 'Empleado no encontrado.',
                    'status' => 404,
                ];
            } else {

                DB::beginTransaction();

                $persona = Persona::findOrFail($empleado['id_empleado']);

                $persona->update([
                    'nombre' => $request->get('nombre'),
                    'paterno' => $request->get('paterno'),
                    'materno' => $request->get('materno'),
                    'telefono' => $request->get('telefono'),
                    'direccion' => $request->get('direccion'),
                    'correo' => $request->get('correo'),
                ]);

                // Obtener la ruta de la imagen anterior y eliminarla (Aún no elimina)
                $rutaImagenAnterior = $persona->imagen; 
                if ($rutaImagenAnterior) {
                    Storage::delete($rutaImagenAnterior);
                }

                // Subir la imagen
                $destinationPath = 'images/persona/empleado/';
                $nombre_campo = 'imagen';
                $this->uploadImage($request, $persona, $nombre_campo, $destinationPath);

                $empleado->update([
                    'sueldo' => $request->get('sueldo'),
                    'compras_realizadas' => $request->get('compras_realizadas'),
                ]);

                DB::commit();

                $response = [
                    'message' => 'Registro actualizado correctamente.',
                    'status' => 200,
                    'data' => $empleado,
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
                'message' => 'Error general al actualizar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function destroy(Empleado $empleado)
    {
        $response = [];
        try {

            $persona = Persona::findOrFail($empleado['id_empleado']);
            $persona->update(['estado' => 0]);
            $empleado->update(['estado' => 0]);
            $response = [
                'message' => 'Registro eliminado correctamente.',
                'status' => 200,
                'msg' => $empleado
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
        $empleados = Empleado::where('estado', 0)->get();
        $empleadoPersona = [];
        $i = 0;
        foreach ($empleados as $empleado) {
            $empleadoPersona[$i]['id_empleado'] = $empleado['id_empleado'];
            $empleadoPersona[$i]['sueldo'] = $empleado['sueldo'];
            $empleadoPersona[$i]['persona'] = Persona::findOrFail($empleado['id_empleado']);
            $i++;
        }

        $data = [
            'data' => $empleadoPersona
        ];

        return response()->json($data);
    }

    public function restaurar(Empleado $empleado)
    {
        $response = [];
        try {

            $persona = Persona::findOrFail($empleado['id_empleado']);
            $persona->update(['estado' => 1]);
            $empleado->update(['estado' => 1]);
            $response = [
                'message' => 'Registro restaurado correctamente.',
                'status' => 200,
                'msg' => $empleado
            ];
        } catch (QueryException | ModelNotFoundException $e) {
            $response = [
                'message' => 'Error en la BD al restaurar el registro.',
                'status' => 500,
                'error' => $e
            ];
        } catch (\Exception $e) {
            $response = [
                'message' => 'Error general al restaurar el registro.',
                'status' => 500,
                'error' => $e
            ];
        }
        return response()->json($response);
    }

    public function uploadImage($request, $data, $imagen, $destinationPath) 
    {
        if ($request->hasFile($imagen)) {
            $file = $request->file($imagen);
            $filename = time() . '-' . $data->getKey() . '.' . $file->getClientOriginalExtension();
            $uploadSuccess = $file->move($destinationPath, $filename);

            if ($uploadSuccess) {
                $data->imagen = $destinationPath . $filename;
                $data->save(); // Guardar los cambios en el modelo
            }
        }
    }
}

