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
        Schema::create('item_menu', function (Blueprint $table) {
            $table->id('id_item_menu');
            $table->string('nombre', 30);
            $table->decimal('precio', 10, 2);
            $table->string('descripcion', 250);
            $table->string('imagen', 250)->nullable();
            $table->tinyInteger('estado')->default(1);
            
            $table->unsignedBigInteger('id_tipo_menu');
            
            $table->foreign('id_tipo_menu')->references('id_tipo_menu')->on('tipo_menu');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_menu');
    }
};
