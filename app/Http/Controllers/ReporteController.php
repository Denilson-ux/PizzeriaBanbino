<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function getReportePedido(Request $request)
    {

        $pedidos = Pedido::select('estado_pedido', 
                            DB::raw('COUNT(id_pedido) as cantidad'), 
                            DB::raw('SUM(monto) as monto'))
                    ->whereBetween('fecha', [$request->get('fechaInicio'), $request->get('fechaFin')])       
                    ->groupBy('estado_pedido')
                    ->get();
        return $pedidos;
    }
}
