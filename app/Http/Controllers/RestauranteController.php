<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestauranteRequest;
use App\Http\Requests\UpdateRestauranteRequest;
use App\Models\Restaurante;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestauranteController extends Controller
{

    public function getIndex()
    {
        return view('pizzeria.restaurante.index');
    }

    #API REST
    public function index()
    {
        $data = Restaurante::orderBy('id_restaurante', 'desc')->first();
        return $data;
    }

    public function store(Request $request)
    {
        $response = [];

        try {
            // Inicia una transacción
            DB::beginTransaction();
            $restaurante = Restaurante::create([
                'nombre' => $request->get('nombre'),
                'direccion' => $request->get('direccion'),
                'telefono' => $request->get('telefono'),
                'correo' => $request->get('correo'),
                'descripcion' => $request->get('descripcion'),
                'latitud' => $request->get('latitud'),
                'longitud' => $request->get('longitud'),
                'horario_apertura' => date('H:i:s'),
                'horario_cierre' => date('H:i:s'),
            ]);

            $destinationPath = 'images/restaurante/';
            $nombre_campo = 'imagen';
            $this->uploadImage($request, $restaurante, $nombre_campo, $destinationPath);

            DB::commit();

            $response = [
                'message' => 'Registro insertado correctamente.',
                'status' => 200,
                'data' => $restaurante,
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

    public function show(Restaurante $restaurante)
    {
        return $restaurante;
    }


    public function update(Request $request, Restaurante $restaurante)
    {
        $response = [];

        try {

            if (!$restaurante) {
                $response = [
                    'message' => 'Restaurante no encontrado.',
                    'status' => 404,
                ];
            } else {

                DB::beginTransaction();

                $restaurante->update([
                    'nombre' => $request->get('nombre'),
                    'direccion' => $request->get('direccion'),
                    'telefono' => $request->get('telefono'),
                    'correo' => $request->get('correo'),
                    'descripcion' => $request->get('descripcion'),
                    'latitud' => $request->get('latitud'),
                    'longitud' => $request->get('longitud'),
                ]);


                //Falta eliminar la imagen anterior
                $destinationPath = 'images/item_menu/';
                $nombre_campo = 'imagen';
                $this->uploadImage($request, $restaurante, $nombre_campo, $destinationPath);

                DB::commit();

                $response = [
                    'message' => 'Registro actualizado correctamente.',
                    'status' => 200,
                    'data' => $restaurante,
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


