<?php

use App\Http\Controllers\AlmacenesController;
use App\Http\Controllers\AsignacionRolPermisoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteWebController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CorreoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ItemMenuController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NotaVentaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RepartidorController;
use App\Http\Controllers\RestauranteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\RolPermisosController;
use App\Http\Controllers\TipoMenuController;
use App\Http\Controllers\TipoPagoController;
use App\Http\Controllers\TipoVehiculoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\IngredienteController;
use Illuminate\Support\Facades\Route;
use App\Mail\Correopizzeria;

/* Rutas actualizadas con módulo de Almacén */

Route::get('/admin', function () { return view('auth.login'); });

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
});

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('tipo-menu', [TipoMenuController::class, 'getIndex']);
    Route::get('item-menu', [ItemMenuController::class, 'getIndex']);
    Route::get('cliente', [ClienteController::class, 'getIndex']);
    Route::get('empleado', [EmpleadoController::class, 'getIndex']);
    Route::get('repartidor', [RepartidorController::class, 'getIndex']);

    Route::get('rol', [RolController::class, 'getIndex']);
    Route::get('rol-error', [RolController::class, 'getError']);

    Route::get('roles-spatie', [RolController::class, 'getRolesSpatie']);
    Route::post('roles-spatie', [RolController::class, 'storeRoleSpatie']);
    Route::put('roles-spatie/{id}', [RolController::class, 'updateRoleSpatie']);
    Route::delete('roles-spatie/{id}', [RolController::class, 'destroyRoleSpatie']);
    Route::get('roles-spatie/{id}', [RolController::class, 'showRoleSpatie']);
    Route::get('permisos-spatie', [RolController::class, 'getPermisosSpatie']);

    /* Asignación de Roles y Permisos */
    Route::get('asignacion-roles-permisos', [AsignacionRolPermisoController::class, 'index'])->name('admin.asignacion-roles-permisos');
    Route::get('api/users-roles', [AsignacionRolPermisoController::class, 'getUsersWithRoles'])->name('api.users-roles');
    Route::post('asignar-rol', [AsignacionRolPermisoController::class, 'asignarRol'])->name('admin.asignar-rol');
    Route::delete('remover-rol', [AsignacionRolPermisoController::class, 'removerRol'])->name('admin.remover-rol');
    Route::post('asignar-permiso', [AsignacionRolPermisoController::class, 'asignarPermiso'])->name('admin.asignar-permiso');
    Route::delete('remover-permiso', [AsignacionRolPermisoController::class, 'removerPermiso'])->name('admin.remover-permiso');

    Route::get('tipo-vehiculo', [TipoVehiculoController::class, 'getIndex']);
    Route::get('tipo-pago', [TipoPagoController::class, 'getIndex']);
    Route::get('vehiculo', [VehiculoController::class, 'getIndex']);

    Route::get('user', [UserController::class, 'getIndex']);
    Route::get('bienvenido', [UserController::class, 'getBienvenido']);
    Route::get('users/with-roles', [UserController::class, 'getUsersWithRoles']);
    Route::get('api/users-con-roles', [UserController::class, 'getUsersWithRoles']);
    Route::get('personas', [UserController::class, 'getPersonasPorTipo'])->name('admin.personas.por_tipo');

    Route::get('menu', [MenuController::class, 'getIndex']);
    Route::get('menu-create', [MenuController::class, 'getCreate']);
    Route::get('menu-edit', [MenuController::class, 'getEdit']);

    Route::get('nota-venta', [NotaVentaController::class, 'getIndex']);
    Route::get('nota-venta-create', [NotaVentaController::class, 'getCreate']);
    Route::get('nota-venta-comprobante-pdf/{id}', [NotaVentaController::class, 'getComprobantePdf']);

    Route::get('pedido', [PedidoController::class, 'getIndex']);
    Route::get('pedido/detalle/{idPedido}', [PedidoController::class, 'getDetallePedido']);
    Route::get('mispedidos', [PedidoController::class, 'getMisPedidos']);

    Route::get('restaurante', [RestauranteController::class, 'getIndex']);

    /* Ingredientes */
    Route::resource('ingredientes', IngredienteController::class);
    Route::get('ingredientes-stock-bajo', [IngredienteController::class, 'stockBajo'])->name('ingredientes.stock_bajo');
    Route::get('ingredientes-reporte-inventario', [IngredienteController::class, 'reporteInventario'])->name('ingredientes.reporte_inventario');
    Route::get('api/ingredientes/buscar', [IngredienteController::class, 'buscar'])->name('ingredientes.buscar');
    Route::get('api/ingredientes/{id}/stock', [IngredienteController::class, 'infoStock'])->name('ingredientes.info_stock');

    /* Proveedores */
    Route::resource('proveedores', ProveedorController::class);

    /* Almacenes */
    Route::resource('almacenes', AlmacenesController::class);
    Route::get('almacenes/{id}/inventario', [AlmacenesController::class, 'inventario'])->name('almacenes.inventario');
    Route::get('almacenes/{id}/movimientos', [AlmacenesController::class, 'movimientos'])->name('almacenes.movimientos');
    // Se elimina la ruta de stock-bajo global por solicitud
    // Route::get('almacenes/{id}/stock-bajo', [AlmacenesController::class, 'stockBajo'])->name('almacenes.stock_bajo');
    Route::post('almacenes/{id}/ajustar-stock', [AlmacenesController::class, 'ajustarStock'])->name('almacenes.ajustar_stock');
    Route::get('api/almacenes/activos', [AlmacenesController::class, 'getAlmacenesActivos'])->name('almacenes.activos');

    /* Compras */
    Route::resource('compras', CompraController::class);
    Route::post('compras/{id}/completar', [CompraController::class, 'completar'])->name('compras.completar');
    Route::post('compras/{id}/cancelar', [CompraController::class, 'cancelar'])->name('compras.cancelar');
    Route::get('api/compras/productos', [CompraController::class, 'getIngredientes'])->name('compras.get_productos');
    Route::get('api/compras/almacenes', [CompraController::class, 'getAlmacenes'])->name('compras.get_almacenes');
});

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    /* API routes if needed */
});

Route::get('/', [ClienteWebController::class, 'getIndex']);
Route::get('form', [ClienteWebController::class, 'getForm']);
Route::get('confirmar', [ClienteWebController::class, 'getConfirmar']);
Route::get('detalle/{idPedido}', [ClienteWebController::class, 'getDetallePedido']);
Route::get('mis-pedidos/{idCliente}', [ClienteWebController::class, 'getMisPedidos']);

Route::get('nota-venta-comprobante/{id}', [NotaVentaController::class, 'getComprobante']);

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/personas', [UserController::class, 'getPersonasPorTipo'])->name('admin.personas.por_tipo');
});