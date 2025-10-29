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
        if (!Schema::hasTable('inventario_almacen')) {
            Schema::create('inventario_almacen', function (Blueprint $table) {
                $table->id('id_inventario');
                $table->unsignedBigInteger('id_almacen');
                $table->unsignedBigInteger('id_ingrediente');
                $table->decimal('stock_actual', 10, 3)->default(0);
                $table->decimal('stock_minimo', 10, 3)->default(0);
                $table->decimal('stock_maximo', 10, 3)->default(1000);
                $table->string('unidad_medida', 50);
                $table->string('ubicacion_fisica', 100)->nullable();
                $table->decimal('costo_unitario_promedio', 10, 2)->default(0);
                $table->date('fecha_ultimo_ingreso')->nullable();
                $table->date('fecha_ultimo_egreso')->nullable();
                $table->date('fecha_vencimiento')->nullable();
                $table->enum('estado', ['activo', 'inactivo'])->default('activo');
                $table->timestamps();

                // FKs: soportar ambas variantes de nombre de tabla de almacenes
                if (Schema::hasTable('almacenes_fisicos')) {
                    $table->foreign('id_almacen')->references('id_almacen')->on('almacenes_fisicos')->onDelete('cascade');
                } else {
                    $table->foreign('id_almacen')->references('id_almacen')->on('almacenes')->onDelete('cascade');
                }
                $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes')->onDelete('cascade');

                $table->unique(['id_almacen', 'id_ingrediente']);
                $table->index(['estado']);
                $table->index(['stock_actual']);
                $table->index(['fecha_vencimiento']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_almacen');
    }
};