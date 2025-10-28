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
        // Crear nueva tabla almacenes (almacenes físicos)
        Schema::create('almacenes', function (Blueprint $table) {
            $table->id('id_almacen');
            $table->string('nombre', 100); // Nombre del almacén físico
            $table->text('descripcion')->nullable(); // Descripción del almacén
            $table->string('ubicacion', 255)->nullable(); // Dirección física
            $table->string('responsable', 100)->nullable(); // Responsable del almacén
            $table->string('telefono', 20)->nullable(); // Teléfono de contacto
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            
            // Índices
            $table->index(['estado']);
            $table->index(['nombre']);
        });
        
        // Modificar tabla compras para agregar almacén destino
        Schema::table('compras', function (Blueprint $table) {
            $table->unsignedBigInteger('id_almacen_destino')->nullable()->after('id_proveedor');
            $table->foreign('id_almacen_destino')->references('id_almacen')->on('almacenes')->onDelete('set null');
            $table->index(['id_almacen_destino']);
        });
        
        // Crear tabla inventario para manejar stock por almacén e ingrediente
        Schema::create('inventario_almacen', function (Blueprint $table) {
            $table->id('id_inventario');
            $table->unsignedBigInteger('id_almacen');
            $table->unsignedBigInteger('id_ingrediente');
            $table->decimal('stock_actual', 10, 3)->default(0);
            $table->decimal('stock_minimo', 10, 3)->default(0);
            $table->decimal('stock_maximo', 10, 3)->default(1000);
            $table->string('unidad_medida', 50);
            $table->string('ubicacion_fisica', 100)->nullable(); // Pasillo, estante, etc.
            $table->decimal('costo_unitario_promedio', 10, 2)->default(0);
            $table->date('fecha_ultimo_ingreso')->nullable();
            $table->date('fecha_ultimo_egreso')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            
            // Claves foráneas
            $table->foreign('id_almacen')->references('id_almacen')->on('almacenes')->onDelete('cascade');
            $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes')->onDelete('cascade');
            
            // Índices
            $table->unique(['id_almacen', 'id_ingrediente']); // Un ingrediente por almacén
            $table->index(['estado']);
            $table->index(['stock_actual']);
            $table->index(['fecha_vencimiento']);
        });
        
        // Insertar almacén por defecto
        DB::table('almacenes')->insert([
            'nombre' => 'Almacén Principal',
            'descripcion' => 'Almacén principal de la pizzería',
            'ubicacion' => 'Local principal',
            'estado' => 'activo',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Migrar datos existentes del almacén antiguo al inventario del almacén principal
        if (Schema::hasTable('almacen')) {
            $almacenPrincipalId = DB::table('almacenes')->where('nombre', 'Almacén Principal')->first()->id_almacen;
            
            $almacenAntiguo = DB::table('almacen')->get();
            foreach ($almacenAntiguo as $item) {
                DB::table('inventario_almacen')->insert([
                    'id_almacen' => $almacenPrincipalId,
                    'id_ingrediente' => $item->id_ingrediente,
                    'stock_actual' => $item->stock_actual,
                    'stock_minimo' => $item->stock_minimo,
                    'stock_maximo' => $item->stock_maximo,
                    'unidad_medida' => $item->unidad_medida,
                    'ubicacion_fisica' => $item->ubicacion_fisica,
                    'costo_unitario_promedio' => $item->costo_unitario_promedio,
                    'fecha_ultimo_ingreso' => $item->fecha_ultimo_ingreso,
                    'fecha_ultimo_egreso' => $item->fecha_ultimo_egreso,
                    'fecha_vencimiento' => $item->fecha_vencimiento,
                    'estado' => $item->estado,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign(['id_almacen_destino']);
            $table->dropColumn('id_almacen_destino');
        });
        
        Schema::dropIfExists('inventario_almacen');
        Schema::dropIfExists('almacenes');
    }
};