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
        Schema::create('menu', function (Blueprint $table) {
            $table->id('id_menu');
            $table->string('nombre', 250);
            $table->string('descripcion', 500);
            $table->date('fecha')->useCurrent(); //guarda la fecha actual en formato a-m-d;
            // $table->string('dia', 20);
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
        Schema::dropIfExists('menu');
    }
};

