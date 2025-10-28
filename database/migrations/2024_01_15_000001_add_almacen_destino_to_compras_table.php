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
        Schema::table('compras', function (Blueprint $table) {
            // Verificar si la columna no existe antes de agregarla
            if (!Schema::hasColumn('compras', 'id_almacen_destino')) {
                $table->unsignedBigInteger('id_almacen_destino')->nullable()->after('id_proveedor');
                $table->foreign('id_almacen_destino')->references('id_almacen')->on('almacenes')->onDelete('set null');
            }
            
            // Agregar campos adicionales si no existen
            if (!Schema::hasColumn('compras', 'aplicar_almacen')) {
                $table->boolean('aplicar_almacen')->default(true)->after('observaciones')->comment('Si debe aplicar automáticamente al inventario del almacén');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            if (Schema::hasColumn('compras', 'id_almacen_destino')) {
                $table->dropForeign(['id_almacen_destino']);
                $table->dropColumn('id_almacen_destino');
            }
            
            if (Schema::hasColumn('compras', 'aplicar_almacen')) {
                $table->dropColumn('aplicar_almacen');
            }
        });
    }
};