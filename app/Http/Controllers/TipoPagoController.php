<?php

namespace App\Http\Controllers;

use App\Models\TipoPago;
use App\Http\Requests\StoreTipoPagoRequest;
use App\Http\Requests\UpdateTipoPagoRequest;
use App\Http\Resources\TipoPagoCollection;
use App\Http\Resources\TipoPagoResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class TipoPagoController extends Controller
{
    
     #WEB
     public function getIndex()
     {
         return view('pizzeria.tipo_pago.index');
     }
 
     #API REST
     public function index()
     {
         $tipoPago = TipoPago::where('estado', 1);
         return new TipoPagoCollection($tipoPago->get());    
     }
 
     public function store(StoreTipoPagoRequest $request)
     {
         $response = [];
         try {
 
             $tipoPago = TipoPago::create($request->all());
             $newRol = new TipoPagoResource($tipoPago);
             $response = [
                 'message' => 'Registro insertado correctamente.',
                 'status' => 200,
                 'msg' => $newRol
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
 
     public function show(TipoPago $tipoPago)
     {
         return new TipoPagoResource($tipoPago);
     }
 
     public function update(UpdateTipoPagoRequest $request, TipoPago $tipoPago)
     {
         $success = $tipoPago->update($request->all());
         $response = [];
         if ($success) {
             $response = [
                 'message' => 'La actualización fue exitosa',
                 'status' => 200,
                 'msg' => $tipoPago
             ];
         } else {
             $response = [
                 'message' => 'La actualización falló',
                 'status' => 500
             ];
         }
         return response()->json($response);
     }
 
     public function destroy(TipoPago $tipoPago)
     {
         $response = [];
         try {
             $tipoPago->update(['estado' => 0]);
 
             $response = [
                 'message' => 'Se eliminó correctamente.',
                 'status' => 200,
                 'msg' => $tipoPago
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
         $rolEliminados = TipoPago::where('estado', 0);
         return new TipoPagoCollection($rolEliminados->get());
     }
 
     public function restaurar(TipoPago $tipoPago)
     {
         $response = [];
         try {
             $tipoPago->update(['estado' => 1]);
 
             $response = [
                 'message' => 'Se restauró correctamente.',
                 'status' => 200,
                 'msg' => $tipoPago
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
