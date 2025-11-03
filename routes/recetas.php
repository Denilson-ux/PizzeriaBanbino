<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecetaController;

Route::middleware(['web','auth'])->prefix('admin')->group(function () {
    Route::get('item-menu/{id}/receta', [RecetaController::class, 'show'])->name('receta.show');
    Route::post('item-menu/{id}/receta', [RecetaController::class, 'agregarIngrediente'])->name('receta.agregar');
    Route::put('item-menu/{id}/receta/{idIngrediente}', [RecetaController::class, 'actualizarIngrediente'])->name('receta.actualizar');
    Route::delete('item-menu/{id}/receta/{idIngrediente}', [RecetaController::class, 'eliminarIngrediente'])->name('receta.eliminar');
});
