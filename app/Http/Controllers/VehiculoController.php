<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Http\Requests\StoreVehiculoRequest;
use App\Http\Requests\UpdateVehiculoRequest;
use App\Http\Resources\VehiculoCollection;
use App\Http\Resources\VehiculoResource;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehiculoController extends Controller
{
    #WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!($user->hasPermissionTo('vehiculos'))) {
            return redirect()->to('admin/rol-error');
        };
        return view('pizzeria.vehiculo.index');
    }

    #API REST
    public function index()
    {
        $datas = Vehiculo::where('estado', 1)
                    ->with(['tipoVehiculo', 'repartidor'])
                    ->get();

        foreach ($datas as $data) {
            if ($data['repartidor'] != null) {
                $data['repartidor']['persona'] = Persona::findOrFail($data['id_repartidor']);
            }
        }
        return new VehiculoCollection($datas);
    }

    public function store(StoreVehiculoRequest $request)
    {
        $response = [];

        try {
            // Inicia una transacción
            DB::beginTransaction();
            $vehiculo = Vehiculo::create([
                'placa' => $request->get('placa'),
                'marca' => $request->get('marca'),
                'modelo' => $request->get('modelo'),
                'color' => $request->get('color'),
                'anio' => $request->get('anio'),
                'id_tipo_vehiculo' => (int)($request->get('id_tipo_vehiculo')),
            ]);


            $destinationPath = 'images/vehiculo/';
            $nombreCampo = 'imagen';
            $this->uploadImage($request, $vehiculo, $nombreCampo, $destinationPath);

            DB::commit();

            $response = [
                'message' => 'Registro insertado correctamente.',
                'status' => 200,
                'data' => $vehiculo,
            ];
        } catch (QueryException | ModelNotFoundException $e) {

            // Deshace la transacción en caso de error
            DB::rollBack();
            $response = [
                'message' => 'Error al insertar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        } catch (\Exception $e) {

            // Deshace la transacción en caso de error
            DB::rollBack();
            $response = [
                'message' => 'Error general al insertar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function show(Vehiculo $vehiculo)
    {
        return new VehiculoResource($vehiculo);
    }


    public function update(UpdateVehiculoRequest $request, Vehiculo $vehiculo)
    {
        $response = [];

        try {

            if (!$vehiculo) {
                $response = [
                    'message' => 'Vehiculo no encontrado.',
                    'status' => 404,
                ];
            } else {

                DB::beginTransaction();

                $vehiculo->update([
                    'placa' => $request->get('placa'),
                    'marca' => $request->get('marca'),
                    'modelo' => $request->get('modelo'),
                    'color' => $request->get('color'),
                    'anio' => $request->get('anio'),
                    'id_repartidor' => (int)($request->get('id_repartidor')) == 0 ? null : (int)($request->get('id_repartidor')),
                    'id_tipo_vehiculo' => (int)($request->get('id_tipo_vehiculo')),
                ]);

                //Falta eliminar la imagen anterior
                $destinationPath = 'images/vehiculo/';
                $nombreCampo = 'imagen';
                $this->uploadImage($request, $vehiculo, $nombreCampo, $destinationPath);

                DB::commit();

                $response = [
                    'message' => 'Registro actualizado correctamente.',
                    'status' => 200,
                    'data' => $vehiculo,
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

    public function updateRepartidor(UpdateVehiculoRequest $request, Vehiculo $vehiculo)
    {
        $response = [];

        try {

            if (!$vehiculo) {
                $response = [
                    'message' => 'Vehiculo no encontrado.',
                    'status' => 404,
                ];
            } else {

                DB::beginTransaction();

                $vehiculo->update([
                    'id_repartidor' => (int)($request->get('id_repartidor')),
                ]);

                DB::commit();

                $response = [
                    'message' => 'Registro actualizado correctamente.',
                    'status' => 200,
                    'data' => $vehiculo,
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

    public function destroy(Vehiculo $vehiculo)
    {
        $response = [];
        try {

            $vehiculo->update(['estado' => 0]);
            $response = [
                'message' => 'Registro eliminado correctamente.',
                'status' => 200,
                'msg' => $vehiculo
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
        $datas = Vehiculo::where('estado', 0)
                    ->with(['tipoVehiculo', 'repartidor'])
                    ->get();

        foreach ($datas as $data) {
            if ($data['repartidor'] != null) {
                $data['repartidor']['persona'] = Persona::findOrFail($data['id_repartidor']);
            }
        }
        return new VehiculoCollection($datas);
    }

    public function restaurar(Vehiculo $vehiculo)
    {
        $response = [];
        try {
            $vehiculo->update(['estado' => 1]);

            $response = [
                'message' => 'Se restauró correctamente.',
                'status' => 200,
                'msg' => $vehiculo
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
