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
        Schema::create('almacen', function (Blueprint $table) {
            $table->id('id_almacen');
            $table->unsignedBigInteger('id_ingrediente');
            $table->decimal('stock_actual', 10, 3)->default(0); // Cantidad actual en almacén
            $table->decimal('stock_minimo', 10, 3)->default(0); // Alerta de stock bajo
            $table->decimal('stock_maximo', 10, 3)->default(1000);
            $table->string('unidad_medida', 50); // gramos, litros, unidades, etc.
            $table->string('ubicacion_fisica', 100)->nullable(); // Ubicación física del ingrediente
            $table->decimal('costo_unitario_promedio', 10, 2)->default(0); // Costo promedio ponderado
            $table->date('fecha_ultimo_ingreso')->nullable();
            $table->date('fecha_ultimo_egreso')->nullable();
            $table->date('fecha_vencimiento')->nullable(); // Para ingredientes perecederos
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            
            // Claves foráneas
            $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes')->onDelete('cascade');
            
            // Índices
            $table->unique('id_ingrediente'); // Un ingrediente solo puede tener un registro en almacén
            $table->index(['estado']);
            $table->index(['stock_actual']);
            $table->index(['fecha_vencimiento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('almacen');
    }
};