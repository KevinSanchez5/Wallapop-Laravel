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
        Schema::create('cliente_favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignGuid('cliente_guid')->constrained('clientes')->onDelete('cascade');
            $table->foreignGuid('producto_guid')->constrained('productos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_favoritos');
    }
};
