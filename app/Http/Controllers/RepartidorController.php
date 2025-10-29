<?php

namespace App\Http\Controllers;

use App\Models\Repartidor;
use App\Http\Requests\StoreRepartidorRequest;
use App\Http\Requests\UpdateRepartidorRequest;
use App\Http\Resources\RepartidorResource;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RepartidorController extends Controller
{
     #WEB
     public function getIndex()
     {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        // Permitir rol Administrador o permiso 'repartidores'
        if (!$user->hasRole('Administrador') && !$user->hasPermissionTo('repartidores')) {
            return redirect()->to('admin/rol-error');
        }
        return view('pizzeria.repartidor.index');
     }
 
     #API REST
     public function index()
     {
         $repartidores = Repartidor::where('estado', 1)->get();
         $repartidoresPersona = [];
         $i = 0;
         foreach ($repartidores as $repartidor) {
            $repartidoresPersona[$i]['id_repartidor'] = $repartidor['id_repartidor'];
            $repartidoresPersona[$i]['licencia_conducir'] = $repartidor['licencia_conducir'];
            $repartidoresPersona[$i]['imagen'] = $repartidor['imagen'];
            $repartidoresPersona[$i]['estado'] = $repartidor['estado'];
            $repartidoresPersona[$i]['created_at'] = $repartidor['created_at'];
            $repartidoresPersona[$i]['updated_at'] = $repartidor['updated_at'];
            $repartidoresPersona[$i]['persona'] = Persona::findOrFail($repartidor['id_repartidor']);
            $i++;
         }
 
         $data = [
             'data' => $repartidoresPersona
         ];
         return response()->json($data);
     }
 
     public function store(StoreRepartidorRequest $request)
     {
         $response = [];
 
         try {
             DB::beginTransaction();
 
             $dataPerson = Persona::create([
                 'ci' => $request->get('ci'),
                 'nombre' => $request->get('nombre'),
                 'paterno' => $request->get('paterno'),
                 'materno' => $request->get('materno'),
                 'telefono' => $request->get('telefono'),
                 'direccion' => $request->get('direccion'),
                 'correo' => $request->get('correo'),
             ]);
 
             // Subir la imagen
             $destinationPath = 'images/persona/repartidor/';
             $nombre_campo = 'imagen';
             $this->uploadImage($request, $dataPerson, $nombre_campo, $destinationPath);
 
             $idPerson = $dataPerson->id_persona;
 
             $data = Repartidor::create([
                 'id_repartidor' => $idPerson,
                 'licencia_conducir' => $request->get('licencia_conducir'),
                 'imagen' => $request->get('imagen'),
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
 
     public function show(Repartidor $repartidor)
     {
         $repartidor['persona'] = Persona::findOrFail($repartidor['id_repartidor']);
         return new RepartidorResource($repartidor);
     }
 
 
     public function update(UpdateRepartidorRequest $request, Repartidor $repartidor)
     {
         $response = [];
 
         try {
             if (!$repartidor) {
                 $response = [
                     'message' => 'Repartidor no encontrado.',
                     'status' => 404,
                 ];
             } else {
 
                 DB::beginTransaction();
 
                 $persona = Persona::findOrFail($repartidor['id_repartidor']);
 
                 $persona->update([
                     'ci' => $request->get('ci'),
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
                 $destinationPath = 'images/persona/repartidor/';
                 $nombre_campo = 'imagen';
                 $this->uploadImage($request, $persona, $nombre_campo, $destinationPath);
 
                 $repartidor->update([
                     'licencia_conducir' => $request->get('licencia_conducir'),
                     'compras_realizadas' => $request->get('compras_realizadas'),
                 ]);
 
                 DB::commit();
 
                 $response = [
                     'message' => 'Registro actualizado correctamente.',
                     'status' => 200,
                     'data' => $repartidor,
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
 
     public function destroy(Repartidor $repartidor)
     {
         $response = [];
         try {
 
             $persona = Persona::findOrFail($repartidor['id_repartidor']);
             $persona->update(['estado' => 0]);
             $repartidor->update(['estado' => 0]);
             $response = [
                 'message' => 'Registro eliminado correctamente.',
                 'status' => 200,
                 'msg' => $repartidor
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
         $repartidores = Repartidor::where('estado', 0)->get();
         $repartidoresPersona = [];
         $i = 0;
         foreach ($repartidores as $repartidor) {
            $repartidoresPersona[$i]['id_repartidor'] = $repartidor['id_repartidor'];
            $repartidoresPersona[$i]['licencia_conducir'] = $repartidor['licencia_conducir'];
            $repartidoresPersona[$i]['estado'] = $repartidor['estado'];
            $repartidoresPersona[$i]['created_at'] = $repartidor['created_at'];
            $repartidoresPersona[$i]['updated_at'] = $repartidor['updated_at'];
            $repartidoresPersona[$i]['persona'] = Persona::findOrFail($repartidor['id_repartidor']);
            $i++;
         }
 
         $data = [
             'data' => $repartidoresPersona
         ];
 
         return response()->json($data);
     }
 
     public function restaurar(Repartidor $repartidor)
     {
         $response = [];
         try {
 
             $persona = Persona::findOrFail($repartidor['id_repartidor']);
             $persona->update(['estado' => 1]);
             $repartidor->update(['estado' => 1]);
             $response = [
                 'message' => 'Registro restaurado correctamente.',
                 'status' => 200,
                 'msg' => $repartidor
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
                 $data->save(); 
             }
         }
     }
}
