<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Http\Requests\StorePedidoRequest;
use App\Http\Requests\UpdatePedidoRequest;
use App\Http\Resources\PedidoCollection;
use App\Http\Resources\PedidoResource;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\ItemMenu;
use App\Models\MenuItemMenu;
use App\Models\Persona;
use App\Models\Repartidor;
use App\Models\Ubicacion;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{

    #WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!($user->hasPermissionTo('pedidos'))) {
            return redirect()->to('admin/rol-error');
        };
        return view('pizzeria.pedido.index');
    }

    public function getDetallePedido(Request $request, $idPedido)
    {
        return view('pizzeria.pedido.detalle_pedido', ['idPedido' => $idPedido]);
    }

    public function getMisPedidos()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!($user->hasPermissionTo('mispedidos'))) {
            return redirect()->to('admin/rol-error');
        };

        $user = $user->load('rol', 'persona');
        return view('pizzeria.pedido.mispedidos', ['user' => $user]);
    }

    #API REST
    public function index()
    {
        $pedidos = Pedido::with([
            'detallePedido.itemMenu.tipoMenu',
            'repartidor',
            'cliente',
            'tipoPago',
            'ubicacion',
        ])->get();

        //Ver la manera de mejorar esta parte, con las relaciones de laravel
        foreach ($pedidos as $pedido) {
            if ($pedido['repartidor'] != null) {
                $idRepartidor = $pedido['repartidor']['id_repartidor'];
                $personaRepartidor = Persona::findOrFail($idRepartidor);
                $pedido['repartidor']['persona'] = $personaRepartidor;
            }


            $idCliente = $pedido['cliente']['id_cliente'];
            $personaCliente = Persona::findOrFail($idCliente);
            $pedido['cliente']['persona'] = $personaCliente;
        }

        return new PedidoCollection($pedidos);
    }

    public function store(StorePedidoRequest $request)
    {
        try {
            $datos = $request->json()->all();

            //Insertar ubicacion
            $ubicacion = Ubicacion::create([
                'latitud' => $datos['latitud'],
                'longitud' => $datos['longitud'],
                'referencia' => $datos['referencia'],
            ]);

            $idUbicacion = $ubicacion->id_ubicacion;

            //Insertar pedido
            $pedido = Pedido::create([
                'monto' => $datos['monto'],
                'fecha' => $datos['fecha'],
                'id_repartidor' => $datos['id_repartidor'],
                'id_cliente' => $datos['id_cliente'],
                'id_tipo_pago' => $datos['id_tipo_pago'],
                'estado_pedido' => $datos['estado_pedido'],
                'nro_transaccion' => $datos['nro_transaccion'],
                'descripcion_pago' => $datos['descripcion_pago'],
                'id_ubicacion' => $idUbicacion,
            ]);

            //insertar detalle pedido
            $idPedido = $pedido->id_pedido;
            $items = $datos['items_menu'];

            foreach ($items as $item) {
                DetallePedido::create([
                    'id_pedido' => $idPedido,
                    'id_item_menu' => $item['id_item_menu'],
                    'id_menu' => $item['id_menu'],
                    'sub_monto' => $item['sub_monto'],
                    'cantidad' => $item['cantidad'],
                ]);


                //actualizar cantidad en menu_item_menu con sql puro (porque es llave compuesta)
                $menuItemMenu = MenuItemMenu::where('id_menu', $item['id_menu'])
                    ->where('id_item_menu', $item['id_item_menu'])
                    ->first();

                $sql = "UPDATE menu_item_menu 
                        SET cantidad = ? 
                        WHERE id_menu = ? 
                        AND id_item_menu = ?";
                $cantidad = $menuItemMenu->cantidad - (int)$item['cantidad'];

                DB::update($sql, [
                    $cantidad,
                    $item['id_menu'],
                    $item['id_item_menu'],
                ]); 
            }

            $response = [
                'message' => 'Registro insertado correctamente.',
                'status' => 200,
                'data' => $pedido,
            ];
        } catch (\Exception $e) {
            $response = [
                'message' => 'Error al insertar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }

        return $response;
    }


    public function show(Pedido $pedido)
    {
        $pedido = $pedido->load(
            'detallePedido.itemMenu.tipoMenu',
            'repartidor',
            'cliente',
            'tipoPago',
            'ubicacion',
        );

        $pedido['cliente']['persona'] = Persona::findOrFail($pedido['cliente']['id_cliente']);
        if ($pedido['repartidor'] != null) {
            $pedido['repartidor']['persona'] = Persona::findOrFail($pedido['repartidor']['id_repartidor']);
        }

        return new PedidoResource($pedido);
    }

    public function showPedidoCliente($idCliente)
    {
        $cliente = Cliente::find($idCliente)->load('pedido');
        return new PedidoResource($cliente);
    }

    public function showPedidoRepartidor($idRepartidor)
    {
        $repartidor = Repartidor::find($idRepartidor)
                ->load([
                    'pedido.cliente',
                    'pedido.detallePedido.itemMenu.tipoMenu',
                    'pedido.tipoPago'
                ]);

        //Corregir las relacioens en laravel para evitar esto (por la herencia)
        foreach ($repartidor['pedido'] as $pedido) {
            $pedido['cliente']['persona'] = Persona::findOrFail($pedido['cliente']['id_cliente']);
            $pedido['repartidor']['persona'] = Persona::findOrFail($pedido['repartidor']['id_repartidor']);
        }
        return new PedidoResource($repartidor);
    }


    public function update(UpdatePedidoRequest $request, Pedido $pedido)
    {
        $response = [];

        try {

            $datos = $request->json()->all();
            if (!$pedido) {
                $response = [
                    'message' => 'Pedido no encontrado.',
                    'status' => 404,
                ];
            } else {

                // Actualizar pedido
                $pedido->update([
                    'id_repartidor' => $datos['id_repartidor'],
                    'estado_pedido' => $datos['estado_pedido'],
                    'descripcion' => $datos['descripcion'],
                ]);

                $response = [
                    'message' => 'Registro actualizado correctamente.',
                    'status' => 200,
                    'data' => $pedido,
                ];
            }
        } catch (\Exception $e) {

            $response = [
                'message' => 'Error al actualizar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }

        return $response;
    }

    public function updatePaypal(UpdatePedidoRequest $request, Pedido $pedido)
    {
        $response = [];

        try {

            $datos = $request->json()->all();
            if (!$pedido) {
                $response = [
                    'message' => 'Pedido no encontrado.',
                    'status' => 404,
                ];
            } else {

                // Actualizar pedido
                $pedido->update([
                    'nro_transaccion' => $datos['nro_transaccion'],
                    'descripcion_pago' => $datos['descripcion_pago'],
                ]);

                $response = [
                    'message' => 'Registro actualizado correctamente.',
                    'status' => 200,
                    'data' => $pedido,
                ];
            }
        } catch (\Exception $e) {

            $response = [
                'message' => 'Error al actualizar el registro.',
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }

        return $response;
    }


    //Sin uso
    public function destroy(Pedido $pedido)
    {
        $response = [];
        try {

            $pedido->update(['estado' => 0]);
            $response = [
                'message' => 'Registro eliminado correctamente.',
                'status' => 200,
                'msg' => $pedido
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

    //Sin uso
    public function eliminados()
    {
        $data = Pedido::where('estado', 0);
        return new PedidoCollection($data->get());
    }

    //Sin uso
    public function restaurar(Pedido $pedido)
    {
        $response = [];
        try {
            $pedido->update(['estado' => 1]);

            $response = [
                'message' => 'Se restauró correctamente.',
                'status' => 200,
                'msg' => $pedido
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
