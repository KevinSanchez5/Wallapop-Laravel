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
        Schema::create('valoraciones', function (Blueprint $table) {
            $table->id();
            $table->string('guid', 11)->unique();
            $table->string('comentario');
            $table->integer('puntuacion');
            $table->foreignId('clienteValorado_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('autor_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valoraciones');
    }

};
