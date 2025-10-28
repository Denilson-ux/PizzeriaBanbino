<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Http\Resources\PersonaCollection;
use App\Http\Resources\PersonaResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personas = Persona::where('estado', 1);
        return new PersonaCollection($personas->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {
        {
            $response = [];
            try {

                $data = Persona::create($request->all());
                $newData = new PersonaResource($data);

                // Subir la imagen
                $destinationPath = 'images/persona/cliente/';
                $nombre_campo = 'imagen';
                $this->uploadImage($request, $data, $nombre_campo, $destinationPath);

                $response = [
                    'message' => 'Registro insertado correctamente.',
                    'status' => 200,
                    'msg' => $newData
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
            return json_encode($response);
        }
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

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        return new PersonaResource($persona);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Persona $persona)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonaRequest $request, Persona $persona)
    {
        // $response = [];
        return $request->all();
        
        // return json_encode($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona)
    {
        $response = [];
        try {

            $persona->update(['estado' => 0]);
            $response = [
                'message' => 'Registro eliminado correctamente.',
                'status' => 200,
                'msg' => $persona
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
}
