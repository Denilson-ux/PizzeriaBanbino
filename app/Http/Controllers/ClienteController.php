<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Http\Resources\ClienteCollection;
use App\Http\Resources\ClienteResource;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    #WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!($user->hasPermissionTo('items'))) {
            return redirect()->to('admin/admin/rol-error');
        };
        return view('pizzeria.cliente.index');
    }

    #API REST 
    public function index() //(Mejorar metodo con las relaciones de laravel)
    {
        $clientes = Cliente::where('estado', 1)->get();
        $clientePersona = [];
        $i = 0;
        foreach ($clientes as $cliente) {
            $clientePersona[$i]['id_cliente'] = $cliente['id_cliente'];
            $clientePersona[$i]['descuento'] = $cliente['descuento'];
            $clientePersona[$i]['compras_realizadas'] = $cliente['compras_realizadas'];
            $clientePersona[$i]['persona'] = Persona::findOrFail($cliente['id_cliente']);
            $i++;
        }

        // Para que retorne los datos y se vea mas pro :v                        
        $data = [
            'data' => $clientePersona
        ];
        return response()->json($data);
    }

    public function store(StoreClienteRequest $request)
    {
        $response = [];

        try {
            // Inicia una transacción
            DB::beginTransaction();

            // Crea la persona
            $dataPerson = Persona::create([
                'nombre' => $request->get('nombre'),
                'paterno' => $request->get('paterno'),
                'materno' => $request->get('materno'),
                'telefono' => $request->get('telefono'),
                'direccion' => $request->get('direccion'),
                'correo' => $request->get('correo'),
            ]);

            // Obtiene el ID de la persona creada
            $idPerson = $dataPerson->id_persona;

            // Crea el cliente asociado a la persona
            $data = Cliente::create([
                'id_cliente' => $idPerson,
                'descuento' => $request->get('descuento'),
                'compras_realizadas' => $request->get('compras_realizadas'),
            ]);

            // Confirma la transacción
            DB::commit();

            // Retorna la respuesta con los datos creados
            $response = [
                'message' => 'Registro insertado correctamente.',
                'status' => 200,
                'data' => $dataPerson,
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

    public function show(Cliente $cliente)
    {
        $cliente['persona'] = Persona::findOrFail($cliente['id_cliente']);
        return new ClienteResource($cliente);
    }


    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $response = [];

        try {
            // Verifica si el cliente existe
            if (!$cliente) {
                $response = [
                    'message' => 'Cliente no encontrado.',
                    'status' => 404,
                ];
            } else {

                // Inicia una transacción
                DB::beginTransaction();

                //Relación cliente persona
                $Persona = Persona::findOrFail($cliente['id_cliente']);

                // Actualiza los datos de la persona asociada al cliente
                $Persona->update([
                    'nombre' => $request->get('nombre'),
                    'paterno' => $request->get('paterno'),
                    'materno' => $request->get('materno'),
                    'telefono' => $request->get('telefono'),
                    'direccion' => $request->get('direccion'),
                    'correo' => $request->get('correo'),
                ]);

                // Actualiza los datos específicos del cliente
                $cliente->update([
                    'descuento' => $request->get('descuento'),
                    'compras_realizadas' => $request->get('compras_realizadas'),
                ]);

                // Confirma la transacción
                DB::commit();

                // Retorna la respuesta con los datos actualizados
                $response = [
                    'message' => 'Registro actualizado correctamente.',
                    'status' => 200,
                    'data' => $cliente,
                ];
            }
        } catch (QueryException | ModelNotFoundException $e) {
            // Deshace la transacción en caso de error
            DB::rollBack();
            $response = [
                'message' => 'Error al actualizar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            // Deshace la transacción en caso de error
            DB::rollBack();
            $response = [
                'message' => 'Error general al actualizar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $response = [];
        try {

            $persona = Persona::findOrFail($cliente['id_cliente']);
            $persona->update(['estado' => 0]);
            $cliente->update(['estado' => 0]);
            $response = [
                'message' => 'Registro eliminado correctamente.',
                'status' => 200,
                'msg' => $cliente
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
        $clientes = Cliente::where('estado', 0)->get();
        $clientePersona = [];
        $i = 0;
        foreach ($clientes as $cliente) {
            $clientePersona[$i]['id_cliente'] = $cliente['id_cliente'];
            $clientePersona[$i]['descuento'] = $cliente['descuento'];
            $clientePersona[$i]['compras_realizadas'] = $cliente['compras_realizadas'];
            $clientePersona[$i]['persona'] = Persona::findOrFail($cliente['id_cliente']);
            $i++;
        }

        $data = [
            'data' => $clientePersona
        ];

        return response()->json($data);
    }

    public function restaurar(Cliente $cliente)
    {
        $response = [];
        try {

            $persona = Persona::findOrFail($cliente['id_cliente']);
            $persona->update(['estado' => 1]);
            $cliente->update(['estado' => 1]);
            $response = [
                'message' => 'Registro restaurado correctamente.',
                'status' => 200,
                'msg' => $cliente
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
}
