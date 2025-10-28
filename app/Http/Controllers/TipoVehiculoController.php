<?php

namespace App\Http\Controllers;

use App\Models\TipoVehiculo;
use App\Http\Requests\StoreTipoVehiculoRequest;
use App\Http\Requests\UpdateTipoVehiculoRequest;
use App\Http\Resources\TipoVehiculoCollection;
use App\Http\Resources\TipoVehiculoResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class TipoVehiculoController extends Controller
{
    #WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!($user->hasPermissionTo('vehiculos'))) {
            return redirect()->to('admin/rol-error');
        };
        return view('pizzeria.tipo_vehiculo.index');
    }

    #API REST
    public function index()
    {
        $roles = TipoVehiculo::where('estado', 1);
        return new TipoVehiculoCollection($roles->get());    
    }

    public function store(StoreTipoVehiculoRequest $request)
    {
        $response = [];
        try {

            $tipoVehiculo = TipoVehiculo::create($request->all());
            $newTipoVehiculo = new TipoVehiculoResource($tipoVehiculo);
            $response = [
                'message' => 'Registro insertado correctamente.',
                'status' => 200,
                'msg' => $newTipoVehiculo
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

    public function show(TipoVehiculo $tipoVehiculo)
    {
        return new TipoVehiculoResource($tipoVehiculo);
    }

    public function update(UpdateTipoVehiculoRequest $request, TipoVehiculo $tipoVehiculo)
    {
        $success = $tipoVehiculo->update($request->all());
        $response = [];
        if ($success) {
            $response = [
                'message' => 'La actualización fue exitosa',
                'status' => 200,
                'msg' => $tipoVehiculo
            ];
        } else {
            $response = [
                'message' => 'La actualización falló',
                'status' => 500
            ];
        }
        return response()->json($response);
    }

    public function destroy(TipoVehiculo $tipoVehiculo)
    {
        $response = [];
        try {
            $tipoVehiculo->update(['estado' => 0]);

            $response = [
                'message' => 'Se eliminó correctamente.',
                'status' => 200,
                'msg' => $tipoVehiculo
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
        $rolEliminados = TipoVehiculo::where('estado', 0);
        return new TipoVehiculoCollection($rolEliminados->get());
    }

    public function restaurar(TipoVehiculo $tipoVehiculo)
    {
        $response = [];
        try {
            $tipoVehiculo->update(['estado' => 1]);

            $response = [
                'message' => 'Se restauró correctamente.',
                'status' => 200,
                'msg' => $tipoVehiculo
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
}
