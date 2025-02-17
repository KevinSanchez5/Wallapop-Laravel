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
        Schema::create('lineasCarrito', function (Blueprint $table) {
            $table->id();
            $table->string('guid');
            $table->json('producto');
            $table->integer('cantidad');
            $table->double('precioTotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lineasCarrito');
    }
};
