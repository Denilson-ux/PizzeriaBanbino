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
        Schema::create('cliente', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cliente')->primary();
            $table->decimal('descuento', 10, 2);
            $table->decimal('compras_realizadas', 10, 2)->default(0);
            $table->tinyInteger('estado')->default(1);

            $table->foreign('id_cliente')
                ->references('id_persona')
                ->on('persona')
                ->onUpdate('cascade');;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
