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
        Schema::create('menu_item_menu', function (Blueprint $table) {
            $table->unsignedBigInteger('id_item_menu');
            $table->unsignedBigInteger('id_menu');
            $table->integer('cantidad');
            
            // Definir las claves foráneas
            $table->foreign('id_item_menu')->references('id_item_menu')->on('item_menu');
            $table->foreign('id_menu')->references('id_menu')->on('menu');

            // Definir la clave primaria compuesta
            $table->primary(['id_item_menu', 'id_menu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_menu');
    }
};
