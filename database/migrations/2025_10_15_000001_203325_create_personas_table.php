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
        Schema::create('persona', function (Blueprint $table) {
            $table->id('id_persona');
            $table->string('ci', 20)->nullable();
            $table->string('nombre', 100);
            $table->string('paterno', 50);
            $table->string('materno', 50)->nullable();
            $table->string('telefono', 15);
            $table->string('direccion', 250)->nullable();
            $table->string('correo', 50);
            $table->string('imagen', 250)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};
