<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IngredienteController;

Route::middleware(['web','auth'])
    ->prefix('admin')
    ->group(function () {
        Route::resource('ingredientes', IngredienteController::class);
        Route::get('ingredientes/stock-bajo', [IngredienteController::class, 'stockBajo'])->name('ingredientes.stock_bajo');
        Route::get('ingredientes/reporte-inventario', [IngredienteController::class, 'reporteInventario'])->name('ingredientes.reporte_inventario');
    });
