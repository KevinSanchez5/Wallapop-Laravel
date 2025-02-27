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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('guid');
            $table->json('comprador');
            $table->json('lineaVentas');
            $table->double('precioTotal');
            $table->enum('estado', [
                'Pendiente',
                'Procesando',
                'Enviado',
                'Entregado',
                'Cancelado',
                'Devuelto'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
