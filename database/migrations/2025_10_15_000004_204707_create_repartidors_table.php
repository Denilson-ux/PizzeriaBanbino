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
        Schema::create('repartidor', function (Blueprint $table) {
            $table->unsignedBigInteger('id_repartidor')->primary();
            $table->string('licencia_conducir', 20);
            $table->string('imagen', 250)->nullable();
            $table->tinyInteger('estado')->default(1);
            
            $table->foreign('id_repartidor')->references('id_persona')->on('persona');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repartidor');
    }
};
