<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ItemMenuController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuItemMenuController;
use App\Http\Controllers\NotaVentaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\RepartidorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RestauranteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\TipoMenuController;
use App\Http\Controllers\TipoPagoController;
use App\Http\Controllers\TipoVehiculoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use App\Models\TipoMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function(){

    #Tipo menú
    Route::apiResource('tipo-menu', TipoMenuController::class);
    Route::get('tipo-menu-eliminados', [TipoMenuController::class, 'eliminados']);
    Route::get('tipo-menu-restaurar/{tipoMenu}', [TipoMenuController::class, 'restaurar']);

    #Item menú
    Route::apiResource('item-menu', ItemMenuController::class);
    Route::post('item-menu/{itemMenu}', [ItemMenuController::class, 'update']); //Porque ocurrio un error al mandar datos por el formulario, por eso de tipo post
    Route::get('item-menu-eliminados', [ItemMenuController::class, 'eliminados']);
    Route::get('item-menu-restaurar/{itemMenu}', [ItemMenuController::class, 'restaurar']);

    #Menú
    Route::apiResource('menu', MenuController::class);
    Route::post('menu/{menu}', [MenuController::class, 'update']); //Porque ocurrio un error al mandar datos por el formulario, por eso de tipo post
    Route::get('menu-eliminados', [MenuController::class, 'eliminados']);
    Route::get('menu-restaurar/{menu}', [MenuController::class, 'restaurar']);
    Route::get('menu-fecha/{fecha}', [MenuController::class, 'indexFecha']);

    #Menú item menu
    Route::apiResource('menu-items', MenuItemMenuController::class);

    #Personas
    Route::apiResource('persona', PersonaController::class);

    #cliente
    Route::apiResource('cliente', ClienteController::class);
    Route::get('cliente-eliminados', [ClienteController::class, 'eliminados']);
    Route::get('cliente-restaurar/{cliente}', [ClienteController::class, 'restaurar']);
    
    #Empleado
    Route::apiResource('empleado', EmpleadoController::class);
    Route::post('empleado/{empleado}', [EmpleadoController::class, 'update']); //Porque ocurrio un error al mandar datos por el formulario, por eso de tipo post
    Route::get('empleado-eliminados', [EmpleadoController::class, 'eliminados']);
    Route::get('empleado-restaurar/{empleado}', [EmpleadoController::class, 'restaurar']);

    #Repartidor
    Route::apiResource('repartidor', RepartidorController::class);
    Route::post('repartidor/{repartidor}', [RepartidorController::class, 'update']); //Porque ocurrio un error al mandar datos por el formulario, por eso de tipo post
    Route::get('repartidor-eliminados', [RepartidorController::class, 'eliminados']);
    Route::get('repartidor-restaurar/{repartidor}', [RepartidorController::class, 'restaurar']);

    #Roles
    Route::apiResource('rol', RolController::class);
    Route::get('rol-eliminados', [RolController::class, 'eliminados']);
    Route::get('rol-restaurar/{rol}', [RolController::class, 'restaurar']);
    Route::get('rol-asignar', [RolController::class, 'asignarRoles']);

    #Tipo Vehiculo
    Route::apiResource('tipo-vehiculo', TipoVehiculoController::class);
    Route::get('tipo-vehiculo-eliminados', [TipoVehiculoController::class, 'eliminados']);
    Route::get('tipo-vehiculo-restaurar/{tipoVehiculo}', [TipoVehiculoController::class, 'restaurar']);

    #Tipo pago
    Route::apiResource('tipo-pago', TipoPagoController::class);
    Route::get('tipo-pago-eliminados', [TipoPagoController::class, 'eliminados']);
    Route::get('tipo-pago-restaurar/{tipoPago}', [TipoPagoController::class, 'restaurar']);

    #vehiculo
    Route::apiResource('vehiculo', VehiculoController::class);
    Route::post('vehiculo/{vehiculo}', [VehiculoController::class, 'update']); //Porque ocurrio un error al mandar datos por el formulario, por eso de tipo post
    Route::get('vehiculo-eliminados', [VehiculoController::class, 'eliminados']);
    Route::get('vehiculo-restaurar/{vehiculo}', [VehiculoController::class, 'restaurar']);

    #user
    Route::apiResource('user', UserController::class);
    Route::post('user/{user}', [UserController::class, 'update']); //Porque ocurrio un error al mandar datos por el formulario, por eso de tipo post
    Route::get('user-eliminados', [UserController::class, 'eliminados']);
    Route::get('user-restaurar/{user}', [UserController::class, 'restaurar']);
    Route::post('user-inicio-sesion', [UserController::class, 'inicioSesion']);
    # Ruta especial para registro de clientes web con rol automático
    Route::post('user-cliente-web', [UserController::class, 'storeClienteWeb']);

    #nota venta
    Route::apiResource('nota-venta', NotaVentaController::class);
    Route::post('nota-venta/{notaVenta}', [NotaVentaController::class, 'update']); //Porque ocurrio un error al mandar datos por el formulario, por eso de tipo post
    Route::get('nota-venta-eliminados', [NotaVentaController::class, 'eliminados']);
    Route::get('nota-venta-restaurar/{notaVenta}', [NotaVentaController::class, 'restaurar']);

    #Ubicación
    Route::apiResource('ubicacion', UbicacionController::class);

    #pedidos
    Route::apiResource('pedido', PedidoController::class);
    Route::post('pedido/{pedido}', [PedidoController::class, 'update']); //Porque ocurrio un error al mandar datos por el formulario, por eso de tipo post
    Route::post('pedido-paypal/{pedido}', [PedidoController::class, 'updatePaypal']);
    Route::get('pedido-eliminados', [PedidoController::class, 'eliminados']);
    Route::get('pedido-restaurar/{pedido}', [PedidoController::class, 'restaurar']);
    Route::get('pedido-cliente/{idCliente}', [PedidoController::class, 'showPedidoCliente']);
    Route::get('pedido-repartidor/{idRepartidor}', [PedidoController::class, 'showPedidoRepartidor']);

    #configuracion restaurante
    Route::apiResource('restaurante', RestauranteController::class);
    Route::post('restaurante/{restaurante}', [RestauranteController::class, 'update']);

    #reportes
    Route::post('reporte-pedido', [ReporteController::class, 'getReportePedido']);

});