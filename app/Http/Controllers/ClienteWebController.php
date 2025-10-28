<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class ClienteWebController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getIndex()
    {
        return view('cliente_web.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getForm()
    {
        return view('cliente_web.form_cliente');
    }

    public function getConfirmar()
    {
        return view('cliente_web.confirmar');
    }

    public function getDetallePedido(Request $request, $idPedido)
    {
        return view('cliente_web.detalle_pedido', ['idPedido' => $idPedido]);
    }

    public function getDetallePedidoPaypal(Request $request, $idPedido, $mensaje)
    {
        return view('cliente_web.detalle_pedido', [
                'idPedido' => $idPedido,
                'mensaje' => $mensaje,
            ]);
    }

    public function getMisPedidos(Request $request, $idCliente)
    {
        return view('cliente_web.mis_pedidos', ['idCliente' => $idCliente]);
    }

    public function getPaypal()
    {
        return view('cliente_web.paypal.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
