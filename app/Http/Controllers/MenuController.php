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
use App\Models\InventarioAlmacen;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                        'id_menu'    => $idMenu,
                        'id_item_menu' => $item['id_item_menu'],
                        'cantidad'   => $item['cantidad'],
                    ]);

                    // Descontar inventario por receta
                    $modeloItem = ItemMenu::with(['recetas' => function($q){
                        $q->select('ingredientes.id_ingrediente','ingredientes.unidad_medida','recetas.cantidad_necesaria','recetas.unidad_receta');
                    }])->findOrFail($item['id_item_menu']);

                    foreach ($modeloItem->recetas as $ing) {
                        $consumo = (float)$ing->pivot->cantidad_necesaria * (int)$item['cantidad'];
                        $uReceta = $ing->pivot->unidad_receta;
                        $uStock  = $ing->unidad_medida;

                        if ($uStock === 'kilogramos' && $uReceta === 'gramos') {
                            $consumo *= 0.001;
                        } elseif ($uStock === 'gramos' && $uReceta === 'kilogramos') {
                            $consumo *= 1000;
                        } elseif ($uStock === 'litros' && $uReceta === 'mililitros') {
                            $consumo *= 0.001;
                        } elseif ($uStock === 'mililitros' && $uReceta === 'litros') {
                            $consumo *= 1000;
                        }

                        $inv = InventarioAlmacen::where('id_ingrediente', $ing->id_ingrediente)
                                ->orderByDesc('stock_actual')
                                ->lockForUpdate()
                                ->first();

                        if (!$inv) {
                            throw new \RuntimeException('STOCK_INSUFICIENTE: Ingrediente sin inventario');
                        }
                        if ($inv->stock_actual < $consumo) {
                            throw new \RuntimeException('STOCK_INSUFICIENTE: '.$ing->nombre);
                        }

                        $inv->reducirStock($consumo);

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

            return [
                'message' => 'Registro insertado y stock actualizado correctamente.',
                'status'  => 200,
                'data'    => $menu,
            ];
        } catch (\Throwable $e) {
            $msg = str_contains($e->getMessage(), 'STOCK_INSUFICIENTE') ? 'Stock insuficiente' : 'Error al insertar el registro.';
            return [
                'message' => $msg,
                'status'  => 500,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
