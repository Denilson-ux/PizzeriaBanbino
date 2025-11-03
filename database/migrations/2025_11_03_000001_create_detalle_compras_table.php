<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id('id_detalle_compra');
            $table->unsignedBigInteger('id_compra');
            $table->unsignedBigInteger('id_ingrediente');
            $table->decimal('cantidad', 12, 3);
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('id_compra')->references('id_compra')->on('compras')->cascadeOnDelete();
            $table->foreign('id_ingrediente')->references('id_ingrediente')->on('ingredientes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};
