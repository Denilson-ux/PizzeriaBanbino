<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use App\Http\Requests\StoreUbicacionRequest;
use App\Http\Requests\UpdateUbicacionRequest;
use App\Http\Resources\UbicacionCollection;
use App\Http\Resources\UbicacionResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class UbicacionController extends Controller
{
    #WEB
    public function getIndex()
    {
        return view('pizzeria.ubicacion.index');
    }

    #API REST
    public function index()
    {
        $data = Ubicacion::where('estado', 1);
        return new UbicacionCollection($data->get());    
    }

    public function store(StoreUbicacionRequest $request)
    {
        $response = [];
        try {

            $ubicacion = Ubicacion::create($request->all());
            $data = new UbicacionResource($ubicacion);
            $response = [
                'message' => 'Registro insertado correctamente.',
                'status' => 200,
                'msg' => $data
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

    public function show(Ubicacion $ubicacion)
    {
        return new UbicacionResource($ubicacion);
    }

    public function update(UpdateUbicacionRequest $request, Ubicacion $ubicacion)
    {
        $success = $ubicacion->update($request->all());
        $response = [];
        if ($success) {
            $response = [
                'message' => 'La actualización fue exitosa',
                'status' => 200,
                'msg' => $ubicacion
            ];
        } else {
            $response = [
                'message' => 'La actualización falló',
                'status' => 500
            ];
        }
        return response()->json($response);
    }

    public function destroy(Ubicacion $ubicacion)
    {
        $response = [];
        try {
            $ubicacion->update(['estado' => 0]);

            $response = [
                'message' => 'Se eliminó correctamente.',
                'status' => 200,
                'msg' => $ubicacion
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
        $rolEliminados = Ubicacion::where('estado', 0);
        return new UbicacionCollection($rolEliminados->get());
    }

    public function restaurar(Ubicacion $ubicacion)
    {
        $response = [];
        try {
            $ubicacion->update(['estado' => 1]);

            $response = [
                'message' => 'Se restauró correctamente.',
                'status' => 200,
                'msg' => $ubicacion
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
