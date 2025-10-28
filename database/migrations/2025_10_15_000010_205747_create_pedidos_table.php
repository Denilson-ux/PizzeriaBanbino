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
        Schema::create('pedido', function (Blueprint $table) {
            $table->id('id_pedido');
            $table->date('fecha');
            $table->decimal('monto', 10, 2);
            $table->string('descripcion', 255)->nullable();
            $table->string('nro_transaccion', 255)->nullable();
            $table->string('descripcion_pago', 255)->nullable();
            $table->string('estado_pedido', 30);
            $table->tinyInteger('estado')->default(1);
            
            $table->unsignedBigInteger('id_ubicacion');
            $table->unsignedBigInteger('id_tipo_pago');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_repartidor')->nullable();
            
            $table->foreign('id_ubicacion')->references('id_ubicacion')->on('ubicacion');
            $table->foreign('id_tipo_pago')->references('id_tipo_pago')->on('tipo_pago');
            $table->foreign('id_cliente')->references('id_cliente')->on('cliente');
            $table->foreign('id_repartidor')->references('id_repartidor')->on('repartidor');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido');
    }
};
