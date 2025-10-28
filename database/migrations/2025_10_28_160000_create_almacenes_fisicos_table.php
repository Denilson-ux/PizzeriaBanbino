<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear tabla almacenes físicos
        Schema::create('almacenes_fisicos', function (Blueprint $table) {
            $table->id('id_almacen');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('ubicacion', 255)->nullable();
            $table->string('responsable', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            
            $table->index(['estado']);
            $table->index(['nombre']);
        });
        
        // Agregar campo almacén destino a compras
        if (Schema::hasTable('compras')) {
            Schema::table('compras', function (Blueprint $table) {
                if (!Schema::hasColumn('compras', 'id_almacen_destino')) {
                    $table->unsignedBigInteger('id_almacen_destino')->nullable()->after('id_proveedor');
                    $table->foreign('id_almacen_destino')->references('id_almacen')->on('almacenes_fisicos')->onDelete('set null');
                    $table->index(['id_almacen_destino']);
                }
            });
        }
        
        // Insertar almacén por defecto
        DB::table('almacenes_fisicos')->insert([
            'nombre' => 'Almacén Principal',
            'descripcion' => 'Almacén principal de la pizzería',
            'ubicacion' => 'Local principal',
            'estado' => 'activo',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('compras')) {
            Schema::table('compras', function (Blueprint $table) {
                if (Schema::hasColumn('compras', 'id_almacen_destino')) {
                    $table->dropForeign(['id_almacen_destino']);
                    $table->dropColumn('id_almacen_destino');
                }
            });
        }
        
        Schema::dropIfExists('almacenes_fisicos');
    }
};