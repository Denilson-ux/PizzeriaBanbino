<?php

namespace App\Http\Controllers;

use App\Filters\TipoMenuFilter;
use App\Models\TipoMenu;
use App\Http\Requests\StoreTipoMenuRequest;
use App\Http\Requests\UpdateTipoMenuRequest;
use App\Http\Resources\TipoMenuCollection;
use App\Http\Resources\TipoMenuResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipoMenuController extends Controller
{
    // WEB
    public function getIndex()
    {
        $usuarioAutenticado = Auth::user();
        $user = User::findOrFail($usuarioAutenticado->id);
        // Permitir Administrador o permiso correcto 'items_menu'
        if (!$user->hasRole('Administrador') && !$user->hasPermissionTo('items_menu')) {
            return redirect()->to('admin/rol-error');
        }
        return view('pizzeria.tipo_menu.index');
    }

    // API REST
    public function index(Request $request)
    {
        $filter = new TipoMenuFilter();
        $queryItems = $filter->transform($request);
        $tipoMenu = TipoMenu::where($queryItems)->where('estado', 1);
        return new TipoMenuCollection($tipoMenu->get());
    }

    public function store(StoreTipoMenuRequest $request)
    {
        $response = [];
        try {
            $tipoMenu = TipoMenu::create($request->all());
            $newTipoMenu = new TipoMenuResource($tipoMenu);
            $response = [
                'message' => 'Registro insertado correctamente.',
                'status' => 200,
                'msg' => $newTipoMenu
            ];
        } catch (QueryException | ModelNotFoundException $e) {
            $response = [
                'message' => 'Error al insertar el registro.',
                'status' => 500,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $response = [
                'message' => 'Error general al insertar el registro.',
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($response, $response['status'] ?? 200);
    }

    public function show(TipoMenu $tipoMenu)
    {
        return new TipoMenuResource($tipoMenu);
    }

    public function update(UpdateTipoMenuRequest $request, TipoMenu $tipoMenu)
    {
        $success = $tipoMenu->update($request->all());
        $response = $success
            ? ['message' => 'La actualización fue exitosa', 'status' => 200, 'msg' => $tipoMenu]
            : ['message' => 'La actualización falló', 'status' => 500];
        return response()->json($response, $response['status']);
    }

    public function destroy(TipoMenu $tipoMenu)
    {
        try {
            $tipoMenu->update(['estado' => 0]);
            return response()->json(['message' => 'Se eliminó correctamente.', 'status' => 200, 'msg' => $tipoMenu]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'La error al eliminar', 'status' => 500, 'error' => $e->getMessage()], 500);
        }
    }

    public function eliminados()
    {
        $tipoMenuEliminados = TipoMenu::where('estado', 0);
        return new TipoMenuCollection($tipoMenuEliminados->get());
    }

    public function restaurar(TipoMenu $tipoMenu)
    {
        try {
            $tipoMenu->update(['estado' => 1]);
            return response()->json(['message' => 'Se restauró correctamente.', 'status' => 200, 'msg' => $tipoMenu]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'La error al resturar.', 'status' => 500, 'error' => $e->getMessage()], 500);
        }
    }
}
