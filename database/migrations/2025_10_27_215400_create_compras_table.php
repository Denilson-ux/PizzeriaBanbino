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
        Schema::create('compras', function (Blueprint $table) {
            $table->id('id_compra');
            $table->string('numero_compra', 20)->unique();
            $table->unsignedBigInteger('id_proveedor');
            $table->unsignedBigInteger('id_usuario');
            $table->date('fecha_compra');
            $table->date('fecha_entrega')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->enum('tipo_compra', ['contado', 'credito'])->default('contado');
            $table->string('numero_factura', 50)->nullable();
            $table->boolean('aplicar_almacen')->default(true); // Si aplica automáticamente al almacén
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Claves foráneas
            $table->foreign('id_proveedor')->references('id_proveedor')->on('proveedores')->onDelete('restrict');
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('restrict');
            
            // Índices
            $table->index(['numero_compra']);
            $table->index(['id_proveedor']);
            $table->index(['id_usuario']);
            $table->index(['estado']);
            $table->index(['fecha_compra']);
            $table->index(['aplicar_almacen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};