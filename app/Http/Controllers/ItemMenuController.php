<?php

namespace App\Http\Controllers;

use App\Models\ItemMenu;
use App\Http\Requests\StoreItemMenuRequest;
use App\Http\Requests\UpdateItemMenuRequest;
use App\Http\Resources\ItemMenuCollection;
use App\Http\Resources\ItemMenuResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemMenuController extends Controller
{

    #WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        if (!($user->hasPermissionTo('items'))) {
            return redirect()->to('admin/rol-error');
        };

        return view('pizzeria.item_menu.index');
    }

    #API REST
    public function index()
    {
        $data = ItemMenu::where('estado', 1)->with('tipoMenu')->with('menus'); //Acceder a la relación de uno a muchos con tipo menu
        return new ItemMenuCollection($data->get());
    }

    public function store(StoreItemMenuRequest $request)
    {
        $response = [];

        try {
            // Inicia una transacción
            DB::beginTransaction();
            $itemMenu = ItemMenu::create([
                'nombre' => $request->get('nombre'),
                'precio' => $request->get('precio'),
                'descripcion' => $request->get('descripcion'),
                'id_tipo_menu' => (int)($request->get('id_tipo_menu')),
            ]);


            $destinationPath = 'images/item_menu/';
            $nombre_campo = 'imagen';
            $this->uploadImage($request, $itemMenu, $nombre_campo, $destinationPath);

            DB::commit();

            $response = [
                'message' => 'Registro insertado correctamente.',
                'status' => 200,
                'data' => $itemMenu,
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

    public function show(ItemMenu $itemMenu)
    {
        return new ItemMenuResource($itemMenu);
    }


    public function update(UpdateItemMenuRequest $request, ItemMenu $itemMenu)
    {
        $response = [];

        try {

            if (!$itemMenu) {
                $response = [
                    'message' => 'ItemMenu no encontrado.',
                    'status' => 404,
                ];
            } else {

                DB::beginTransaction();

                $itemMenu->update([
                    'nombre' => $request->get('nombre'),
                    'precio' => $request->get('precio'),
                    'descripcion' => $request->get('descripcion'),
                    'id_tipo_menu' => $request->get('id_tipo_menu'),
                ]);


                //Falta eliminar la imagen anterior
                $destinationPath = 'images/item_menu/';
                $nombre_campo = 'imagen';
                $this->uploadImage($request, $itemMenu, $nombre_campo, $destinationPath);

                DB::commit();

                $response = [
                    'message' => 'Registro actualizado correctamente.',
                    'status' => 200,
                    'data' => $itemMenu,
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

    public function destroy(ItemMenu $itemMenu)
    {
        $response = [];
        try {

            $itemMenu->update(['estado' => 0]);
            $response = [
                'message' => 'Registro eliminado correctamente.',
                'status' => 200,
                'msg' => $itemMenu
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
        $data = ItemMenu::where('estado', 0)->with('tipoMenu'); //Acceder a la relación de uno a muchos con tipo menu
        return new ItemMenuCollection($data->get());
    }

    public function restaurar(ItemMenu $itemMenu)
    {
        $response = [];
        try {
            $itemMenu->update(['estado' => 1]);

            $response = [
                'message' => 'Se restauró correctamente.',
                'status' => 200,
                'msg' => $itemMenu
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
