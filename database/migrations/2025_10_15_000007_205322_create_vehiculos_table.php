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
        Schema::create('vehiculo', function (Blueprint $table) {
            $table->id('id_vehiculo');
            $table->string('placa', 10);
            $table->string('marca', 250);
            $table->string('modelo', 250);
            $table->string('color', 15);
            $table->integer('anio')->nullable();
            $table->string('imagen', 250)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->unsignedBigInteger('id_repartidor')->nullable();
            $table->unsignedBigInteger('id_tipo_vehiculo');

            $table->foreign('id_repartidor')->references('id_repartidor')->on('repartidor');
            $table->foreign('id_tipo_vehiculo')->references('id_tipo_vehiculo')->on('tipo_vehiculo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculo');
    }
};
