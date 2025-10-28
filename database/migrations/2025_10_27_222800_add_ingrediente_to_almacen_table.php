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
        Schema::table('almacen', function (Blueprint $table) {
            // Agregar columna para ingredientes
            $table->unsignedBigInteger('id_ingrediente')->nullable()->after('id_item_menu');
            $table->enum('tipo_producto', ['item_menu', 'ingrediente'])->default('item_menu')->after('id_ingrediente');
            
            // Clave foránea
            $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes')->onDelete('cascade');
            
            // Índices
            $table->index(['id_ingrediente']);
            $table->index(['tipo_producto']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('almacen', function (Blueprint $table) {
            $table->dropForeign(['id_ingrediente']);
            $table->dropIndex(['id_ingrediente']);
            $table->dropIndex(['tipo_producto']);
            $table->dropColumn(['id_ingrediente', 'tipo_producto']);
        });
    }
};