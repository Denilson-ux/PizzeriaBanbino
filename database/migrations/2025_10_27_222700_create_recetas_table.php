<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->id('id_receta');
            $table->unsignedBigInteger('id_item_menu');
            $table->unsignedBigInteger('id_ingrediente');
            $table->decimal('cantidad_necesaria', 10, 3); // Cantidad del ingrediente necesaria
            $table->enum('unidad_receta', ['gramos', 'kilogramos', 'mililitros', 'litros', 'unidades', 'porciones']);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Claves foráneas
            $table->foreign('id_item_menu')->references('id_item_menu')->on('item_menu')->onDelete('cascade');
            $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes')->onDelete('cascade');
            
            // Índices
            $table->index(['id_item_menu']);
            $table->index(['id_ingrediente']);
            
            // Índice único para evitar duplicados
            $table->unique(['id_item_menu', 'id_ingrediente'], 'receta_unica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};