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
        Schema::create('detalle_venta', function (Blueprint $table) {
            $table->decimal('sub_monto', 10, 2);
            $table->integer('cantidad');
            $table->tinyInteger('estado')->default(1);
            
            $table->unsignedBigInteger('id_nota_venta');
            $table->unsignedBigInteger('id_item_menu');
            $table->unsignedBigInteger('id_menu');

            // Definir las claves foráneas
            $table->foreign('id_nota_venta')->references('id_nota_venta')->on('nota_venta');
            $table->foreign(['id_item_menu', 'id_menu'])->references(['id_item_menu', 'id_menu'])->on('menu_item_menu');

            // Definir la clave primaria compuesta
            $table->primary(['id_nota_venta', 'id_item_menu', 'id_menu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_venta');
    }
};
