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
            $table->string('guid', 11)->unique();
            $table->json('comprador');
            $table->json('lineaVentas');
            $table->double('precioTotal');
            $table->string('payment_intent_id')->nullable();
            $table->enum('estado', [
                'Pendiente',
                'Procesando',
                'Enviado',
                'Entregado',
                'Cancelado'
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
