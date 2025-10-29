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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{

    #WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!$user->hasRole('Administrador') && !$user->hasPermissionTo('pedidos')) {
            return redirect()->to('admin/rol-error');
        }
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
        // Usar el permiso 'pedidos' o permitir Administrador; no existe 'mispedidos' en el seeder
        if (!$user->hasRole('Administrador') && !$user->hasPermissionTo('pedidos')) {
            return redirect()->to('admin/rol-error');
        }

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
            
            // Log para debugging
            Log::info('Datos recibidos para pedido:', $datos);
            
            // Validar que el cliente existe
            $cliente = Cliente::find($datos['id_cliente']);
            if (!$cliente) {
                return [
                    'message' => 'Cliente no encontrado con ID: ' . $datos['id_cliente'],
                    'status' => 400,
                    'error' => 'Cliente inexistente'
                ];
            }
            
            // Validar que los items existen
            if (!isset($datos['items_menu']) || empty($datos['items_menu'])) {
                return [
                    'message' => 'No hay items en el pedido',
                    'status' => 400,
                    'error' => 'Items faltantes'
                ];
            }
            
            // Validar ubicación
            if (!isset($datos['latitud']) || !isset($datos['longitud']) || !isset($datos['referencia'])) {
                return [
                    'message' => 'Datos de ubicación incompletos',
                    'status' => 400,
                    'error' => 'Ubicación faltante'
                ];
            }

            // Crear ubicación
            $ubicacion = Ubicacion::create([
                'latitud' => $datos['latitud'],
                'longitud' => $datos['longitud'],
                'referencia' => $datos['referencia'],
            ]);

            $idUbicacion = $ubicacion->id_ubicacion;

            // Preparar datos del pedido con valores por defecto
            $datosPedido = [
                'monto' => $datos['monto'] ?? 0,
                'fecha' => $datos['fecha'] ?? date('Y-m-d'),
                'id_repartidor' => $datos['id_repartidor'] ?? null,
                'id_cliente' => $datos['id_cliente'],
                'id_tipo_pago' => $datos['id_tipo_pago'] ?? 1, // 1 = efectivo por defecto
                'estado_pedido' => $datos['estado_pedido'] ?? 'Pendiente',
                'nro_transaccion' => $datos['nro_transaccion'] ?? null,
                'descripcion_pago' => $datos['descripcion_pago'] ?? null,
                'id_ubicacion' => $idUbicacion,
                'estado' => 1 // Activo por defecto
            ];
            
            Log::info('Datos del pedido a crear:', $datosPedido);

            $pedido = Pedido::create($datosPedido);

            $idPedido = $pedido->id_pedido;
            $items = $datos['items_menu'];

            foreach ($items as $item) {
                // Validar que el item tenga los datos necesarios
                if (!isset($item['id_item_menu']) || !isset($item['id_menu'])) {
                    Log::warning('Item sin datos completos:', $item);
                    continue;
                }
                
                DetallePedido::create([
                    'id_pedido' => $idPedido,
                    'id_item_menu' => $item['id_item_menu'],
                    'id_menu' => $item['id_menu'],
                    'sub_monto' => $item['sub_monto'] ?? 0,
                    'cantidad' => $item['cantidad'] ?? 1,
                ]);

                // Actualizar stock solo si existe el registro
                $menuItemMenu = MenuItemMenu::where('id_menu', $item['id_menu'])
                    ->where('id_item_menu', $item['id_item_menu'])
                    ->first();

                if ($menuItemMenu) {
                    $sql = "UPDATE menu_item_menu 
                            SET cantidad = ? 
                            WHERE id_menu = ? 
                            AND id_item_menu = ?";
                    $cantidad = $menuItemMenu->cantidad - (int)($item['cantidad'] ?? 1);

                    DB::update($sql, [
                        max(0, $cantidad), // No permitir cantidades negativas
                        $item['id_menu'],
                        $item['id_item_menu'],
                    ]); 
                }
            }

            $response = [
                'message' => 'Pedido registrado correctamente.',
                'status' => 200,
                'data' => $pedido,
            ];
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error de BD al crear pedido:', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'datos' => $datos ?? []
            ]);
            
            $response = [
                'message' => 'Error en la base de datos al crear el pedido.',
                'status' => 500,
                'error' => 'Error de BD: ' . $e->getMessage(),
            ];
            
        } catch (\Exception $e) {
            Log::error('Error general al crear pedido:', [
                'error' => $e->getMessage(),
                'datos' => $datos ?? []
            ]);
            
            $response = [
                'message' => 'Error al crear el pedido.',
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

    public function eliminados()
    {
        $data = Pedido::where('estado', 0);
        return new PedidoCollection($data->get());
    }

    public function restaurar(Pedido $pedido)
    {
        try {
            $pedido->update(['estado' => 1]);
            return response()->json(['message' => 'Se restauró correctamente.', 'status' => 200, 'msg' => $pedido]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'La error al resturar.', 'status' => 500, 'error' => $e]);
        }
    }
}