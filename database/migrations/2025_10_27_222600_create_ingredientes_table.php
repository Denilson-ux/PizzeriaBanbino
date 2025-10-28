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
        Schema::create('ingredientes', function (Blueprint $table) {
            $table->id('id_ingrediente');
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->enum('unidad_medida', ['gramos', 'kilogramos', 'mililitros', 'litros', 'unidades', 'porciones']);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->enum('categoria', ['lacteos', 'carnes', 'vegetales', 'harinas', 'condimentos', 'bebidas', 'otros'])->default('otros');
            $table->boolean('es_perecedero')->default(false);
            $table->integer('dias_vencimiento')->nullable(); // Días de vencimiento si es perecedero
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['nombre']);
            $table->index(['estado']);
            $table->index(['categoria']);
            $table->index(['es_perecedero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredientes');
    }
};