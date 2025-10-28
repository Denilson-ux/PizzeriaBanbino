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
        Schema::create('item_menu_ingredientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_item_menu');
            $table->unsignedBigInteger('id_ingrediente');
            $table->decimal('cantidad_necesaria', 10, 3); // Cantidad del ingrediente necesaria para hacer el producto
            $table->string('unidad_medida', 50); // gramos, litros, unidades, etc.
            $table->text('notas')->nullable(); // Notas especiales para la preparación
            $table->boolean('es_opcional')->default(false); // Si el ingrediente es opcional
            $table->timestamps();
            
            // Claves foráneas
            $table->foreign('id_item_menu')->references('id_item_menu')->on('item_menu')->onDelete('cascade');
            $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes')->onDelete('cascade');
            
            // Índices
            $table->unique(['id_item_menu', 'id_ingrediente']); // Un ingrediente solo una vez por receta
            $table->index(['id_item_menu']);
            $table->index(['id_ingrediente']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_menu_ingredientes');
    }
};