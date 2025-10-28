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
        Schema::create('nota_venta', function (Blueprint $table) {
            $table->id('id_nota_venta');
            $table->decimal('monto', 10, 2);
            $table->date('fecha'); //->useCurrent();
            $table->tinyInteger('estado')->default(1);
            
            $table->unsignedBigInteger('id_empleado');
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->unsignedBigInteger('id_tipo_pago')->nullable();
            
            $table->foreign('id_empleado')->references('id_empleado')->on('empleado');
            $table->foreign('id_cliente')->references('id_cliente')->on('cliente');
            $table->foreign('id_tipo_pago')->references('id_tipo_pago')->on('tipo_pago');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_venta');
    }
};
