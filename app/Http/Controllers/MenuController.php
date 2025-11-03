<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\MenuCollection;
use App\Http\Resources\MenuResource;
use App\Models\MenuItemMenu;
use App\Models\User;
use App\Models\ItemMenu;
use App\Models\Ingrediente;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    #WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!$user->hasRole('Administrador') && !$user->hasPermissionTo('items_menu')) {
            return redirect()->to('admin/rol-error');
        }

        return view('pizzeria.menu.index');
    }

    public function getCreate()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!$user->hasRole('Administrador') && !$user->hasPermissionTo('items_menu')) {
            return redirect()->to('admin/rol-error');
        }

        return view('pizzeria.menu.create');
    }
    public function getEdit() 
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!$user->hasRole('Administrador') && !$user->hasPermissionTo('items_menu')) {
            return redirect()->to('admin/rol-error');
        }

        return view('pizzeria.menu.edit');
    }

    #API REST
    public function index()
    {
        $menus = Menu::where('estado', 1)
                    ->with('itemMenus')
                    ->orderBy('created_at', 'desc')
                    ->get();
        return new MenuCollection($menus);
    }

    public function indexFecha(Request $request, $fecha)
    {
        $menus = Menu::where('fecha', $fecha)
                    ->where('estado', 1)
                    ->with('itemMenus')
                    ->orderBy('created_at', 'desc')
                    ->get();
        return new MenuCollection($menus);
    }

    public function store(StoreMenuRequest $request)
    {
        try {
            $datos = $request->json()->all();

            DB::transaction(function () use (&$menu, $datos) {
                $menu = Menu::create([
                    'nombre' => $datos['nombre'],
                    'descripcion' => $datos['descripcion'],
                    'fecha' => $datos['fecha'],
                ]);

                $idMenu = $menu->id_menu;
                $items = $datos['items_menu'];

                foreach ($items as $item) {
                    MenuItemMenu::create([
                        'id_menu' => $idMenu,
                        'id_item_menu' => $item['id_item_menu'],
                        'cantidad' => $item['cantidad'],
                    ]);

                    // Descontar inventario por receta
                    $modeloItem = ItemMenu::with(['recetas' => function($q){
                        $q->select('ingredientes.id_ingrediente','ingredientes.unidad_medida','receta_item_menu.cantidad_necesaria','receta_item_menu.unidad_receta');
                    }])->findOrFail($item['id_item_menu']);

                    foreach ($modeloItem->recetas as $ing) {
                        $consumo = (float)$ing->pivot->cantidad_necesaria * (int)$item['cantidad'];
                        $uReceta = $ing->pivot->unidad_receta;
                        $uStock = $ing->unidad_medida; // unidad base guardada en ingrediente

                        // conversiones simples kg<->g y l<->ml
                        if ($uStock === 'kilogramos' && $uReceta === 'gramos') {
                            $consumo *= 0.001;
                        } elseif ($uStock === 'gramos' && $uReceta === 'kilogramos') {
                            $consumo *= 1000;
                        } elseif ($uStock === 'litros' && $uReceta === 'mililitros') {
                            $consumo *= 0.001;
                        } elseif ($uStock === 'mililitros' && $uReceta === 'litros') {
                            $consumo *= 1000;
                        }

                        // verificar stock suficiente y decrementar de forma atómica
                        $afectados = DB::table('ingredientes')
                            ->where('id_ingrediente', $ing->id_ingrediente)
                            ->where('stock', '>=', $consumo)
                            ->decrement('stock', $consumo);

                        if ($afectados === 0) {
                            throw new \RuntimeException('Stock insuficiente para '.$ing->nombre);
                        }

                        // registrar movimiento (si existe la tabla; si no, omitir)
                        if (Schema::hasTable('movimientos_inventario')) {
                            DB::table('movimientos_inventario')->insert([
                                'id_ingrediente' => $ing->id_ingrediente,
                                'tipo' => 'salida',
                                'cantidad' => $consumo,
                                'unidad' => $uStock,
                                'motivo' => 'Consumo por menú: '.$menu->id_menu,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            });

            $response = [
                'message' => 'Registro insertado y stock actualizado correctamente.',
                'status' => 200,
                'data' => $menu,
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


    public function show(Menu $menu)
    {
        return new MenuResource($menu->load('itemMenus'));
    }


    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $response = [];
        $datos = $request->json()->all();
        try {
            if (!$menu) {
                $response = [
                    'message' => 'Menu no encontrado.',
                    'status' => 404,
                ];
            } else {

                // Actualizar el menú
                $menu->update([
                    'nombre' => $datos['nombre'],
                    'descripcion' => $datos['descripcion'],
                ]);

                // Actualizar menu_item_menu
                $idMenu = $menu->id_menu;
                $items = $datos['items_menu'];

                // Insertar nuevos registros
                foreach ($items as $item) {
                    $itemUpdate = MenuItemMenu::where('id_menu', $idMenu)
                                  ->where('id_item_menu', $item['id_item_menu'])
                                  ->first();
                    if ($itemUpdate) {
                        MenuItemMenu::where('id_menu', $idMenu)
                                ->where('id_item_menu', $item['id_item_menu'])
                                ->update(['cantidad' => $item['cantidad']]);
                    } else {
                        MenuItemMenu::create([
                            'id_menu' => $idMenu,
                            'id_item_menu' => $item['id_item_menu'],
                            'cantidad' => $item['cantidad'],
                        ]);
                    }
                }


                $response = [
                    'message' => 'Registro actualizado correctamente.',
                    'status' => 200,
                    'data' => $menu,
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



    public function destroy(Menu $menu)
    {
        $response = [];
        try {

            $menu->update(['estado' => 0]);
            $response = [
                'message' => 'Registro eliminado correctamente.',
                'status' => 200,
                'msg' => $menu
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
        $data = Menu::where('estado', 0)->with('itemMenus');
        return new MenuCollection($data->get());
    }

    public function restaurar(Menu $menu)
    {
        $response = [];
        try {
            $menu->update(['estado' => 1]);

            $response = [
                'message' => 'Se restauró correctamente.',
                'status' => 200,
                'msg' => $menu
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
