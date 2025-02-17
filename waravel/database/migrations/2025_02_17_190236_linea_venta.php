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
        Schema::create('lineaVentas', function (Blueprint $table) {
            $table->id();
            $table->string('guid');
            $table->json('vendedor');
            $table->integer('cantidad');
            $table->json('producto');
            $table->double('precio');
            $table->double('precioTotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lineaVentas');
    }
};
