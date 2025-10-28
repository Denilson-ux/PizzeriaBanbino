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
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id('id_detalle_compra');
            $table->unsignedBigInteger('id_compra');
            $table->unsignedBigInteger('id_ingrediente'); // Cambiado de id_almacen a id_ingrediente
            $table->decimal('cantidad', 10, 3); // Cantidad comprada
            $table->string('unidad_medida', 50); // gramos, kilogramos, litros, etc.
            $table->decimal('precio_unitario', 10, 2); // Precio por unidad de medida
            $table->decimal('subtotal', 10, 2); // cantidad * precio_unitario
            $table->date('fecha_vencimiento')->nullable(); // Para ingredientes perecederos
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Claves foráneas
            $table->foreign('id_compra')->references('id_compra')->on('compras')->onDelete('cascade');
            $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes')->onDelete('cascade');
            
            // Índices
            $table->index(['id_compra']);
            $table->index(['id_ingrediente']);
            $table->index(['fecha_vencimiento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};