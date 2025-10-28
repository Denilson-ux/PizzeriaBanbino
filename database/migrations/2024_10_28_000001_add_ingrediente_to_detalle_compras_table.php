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
        Schema::table('detalle_compras', function (Blueprint $table) {
            // Agregar campo para ingredientes
            $table->unsignedBigInteger('id_ingrediente')->nullable()->after('id_item_menu');
            
            // Agregar índice
            $table->index('id_ingrediente');
            
            // Agregar clave foránea
            $table->foreign('id_ingrediente')
                  ->references('id_ingrediente')
                  ->on('ingredientes')
                  ->onDelete('cascade');
            
            // Modificar el campo id_item_menu para que sea nullable
            $table->unsignedBigInteger('id_item_menu')->nullable()->change();
        });
        
        // Agregar restricción para asegurar que solo uno de los dos campos tenga valor
        DB::statement('ALTER TABLE detalle_compras ADD CONSTRAINT chk_detalle_compras_producto CHECK (
            (id_item_menu IS NOT NULL AND id_ingrediente IS NULL) OR 
            (id_item_menu IS NULL AND id_ingrediente IS NOT NULL)
        )');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_compras', function (Blueprint $table) {
            // Eliminar restricción
            DB::statement('ALTER TABLE detalle_compras DROP CONSTRAINT IF EXISTS chk_detalle_compras_producto');
            
            // Eliminar clave foránea e índice
            $table->dropForeign(['id_ingrediente']);
            $table->dropIndex(['id_ingrediente']);
            
            // Eliminar campo
            $table->dropColumn('id_ingrediente');
            
            // Revertir id_item_menu a not nullable
            $table->unsignedBigInteger('id_item_menu')->nullable(false)->change();
        });
    }
};