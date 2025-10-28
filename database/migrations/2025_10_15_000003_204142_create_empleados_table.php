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
        Schema::create('empleado', function (Blueprint $table) {
            $table->unsignedBigInteger('id_empleado')->primary();
            $table->decimal('sueldo', 10, 2);
            $table->tinyInteger('estado')->default(1);
            
            $table->foreign('id_empleado')->references('id_persona')->on('persona');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado');
    }
};
