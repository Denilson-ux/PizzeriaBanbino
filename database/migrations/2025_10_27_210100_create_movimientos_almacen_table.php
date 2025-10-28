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
        Schema::create('movimientos_almacen', function (Blueprint $table) {
            $table->id('id_movimiento');
            $table->unsignedBigInteger('id_ingrediente');
            $table->enum('tipo_movimiento', ['ingreso', 'egreso', 'ajuste', 'merma']); 
            $table->decimal('cantidad', 10, 3); // Cantidad del movimiento
            $table->string('unidad_medida', 50);
            $table->decimal('costo_unitario', 10, 2)->nullable(); // Para ingresos
            $table->decimal('stock_anterior', 10, 3); // Stock antes del movimiento
            $table->decimal('stock_posterior', 10, 3); // Stock después del movimiento
            $table->string('referencia_tipo', 50)->nullable(); // 'compra', 'venta', 'ajuste', 'merma'
            $table->unsignedBigInteger('referencia_id')->nullable(); // ID de la compra, venta, etc.
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('usuario_id'); // Usuario que hizo el movimiento
            $table->timestamp('fecha_movimiento');
            $table->timestamps();
            
            // Claves foráneas
            $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            
            // Índices
            $table->index(['id_ingrediente']);
            $table->index(['tipo_movimiento']);
            $table->index(['fecha_movimiento']);
            $table->index(['referencia_tipo', 'referencia_id']);
            $table->index(['usuario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_almacen');
    }
};