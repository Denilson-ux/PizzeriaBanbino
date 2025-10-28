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
        Schema::create('detalle_pedido', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pedido');
            $table->decimal('sub_monto', 10, 2);
            $table->integer('cantidad');
            $table->tinyInteger('estado')->default(1);
            
            $table->unsignedBigInteger('id_item_menu');
            $table->unsignedBigInteger('id_menu');

            // Definir las claves foráneas
            $table->foreign('id_pedido')->references('id_pedido')->on('pedido');
            $table->foreign(['id_item_menu', 'id_menu'])->references(['id_item_menu', 'id_menu'])->on('menu_item_menu');

            // Definir la clave primaria compuesta
            $table->primary(['id_pedido', 'id_item_menu', 'id_menu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pedido');
    }
};
