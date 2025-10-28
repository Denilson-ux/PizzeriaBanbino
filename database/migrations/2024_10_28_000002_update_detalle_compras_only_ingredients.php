<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Asegurar que la tabla existe antes que nada
        if (!Schema::hasTable('detalle_compras')) {
            return; // nada que hacer
        }

        // 1) Intentar eliminar el CHECK si existe (ignorar error si no existe)
        try { DB::statement('ALTER TABLE detalle_compras DROP CHECK chk_detalle_compras_producto'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE detalle_compras DROP CONSTRAINT chk_detalle_compras_producto'); } catch (\Throwable $e) {}

        // 2) Si la columna existe, eliminar cualquier FK y la columna
        if (Schema::hasColumn('detalle_compras', 'id_item_menu')) {
            // FK por convención
            try { DB::statement('ALTER TABLE detalle_compras DROP FOREIGN KEY detalle_compras_id_item_menu_foreign'); } catch (\Throwable $e) {}
            // FK común en MySQL
            try { DB::statement('ALTER TABLE detalle_compras DROP FOREIGN KEY detalle_compras_ibfk_1'); } catch (\Throwable $e) {}

            // Drop via Schema helper (si sigue existiendo la FK con nombre distinto)
            try {
                Schema::table('detalle_compras', function (Blueprint $table) {
                    $table->dropForeign(['id_item_menu']);
                });
            } catch (\Throwable $e) {}

            // Finalmente, eliminar la columna
            try {
                Schema::table('detalle_compras', function (Blueprint $table) {
                    $table->dropColumn('id_item_menu');
                });
            } catch (\Throwable $e) {}
        }

        // 3) Asegurar que id_ingrediente sea NOT NULL (si la columna existe)
        if (Schema::hasColumn('detalle_compras', 'id_ingrediente')) {
            try {
                Schema::table('detalle_compras', function (Blueprint $table) {
                    $table->unsignedBigInteger('id_ingrediente')->nullable(false)->change();
                });
            } catch (\Throwable $e) {}
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('detalle_compras')) {
            return;
        }

        // 1) Restaurar columna id_item_menu si no existe
        if (!Schema::hasColumn('detalle_compras', 'id_item_menu')) {
            Schema::table('detalle_compras', function (Blueprint $table) {
                $table->unsignedBigInteger('id_item_menu')->nullable()->after('id_compra');
                try { $table->foreign('id_item_menu')->references('id_item_menu')->on('item_menu')->onDelete('cascade'); } catch (\Throwable $e) {}
            });
        }

        // 2) Hacer id_ingrediente nullable
        if (Schema::hasColumn('detalle_compras', 'id_ingrediente')) {
            try {
                Schema::table('detalle_compras', function (Blueprint $table) {
                    $table->unsignedBigInteger('id_ingrediente')->nullable()->change();
                });
            } catch (\Throwable $e) {}
        }

        // 3) Intentar recrear el CHECK (si el motor lo soporta)
        try {
            DB::statement('ALTER TABLE detalle_compras ADD CONSTRAINT chk_detalle_compras_producto CHECK ((id_item_menu IS NOT NULL AND id_ingrediente IS NULL) OR (id_item_menu IS NULL AND id_ingrediente IS NOT NULL))');
        } catch (\Throwable $e) {}
    }
};