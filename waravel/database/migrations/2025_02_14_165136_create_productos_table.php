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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('guid');
            $table->foreignId('vendedor_id')->constrained('clientes')->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion');
            $table->enum('estadoFisico', ['Nuevo', 'Usado', 'Deteriorado']);
            $table->decimal('precio');
            $table->enum('categoria', [
                'Tecnologia',
                'Ropa',
                'Hogar',
                'Coleccionismo',
                'Vehiculos',
                'Videojuegos',
                'Musica',
                'Deporte',
                'Cine',
                'Cocina'
            ]);
            $table->enum('estado', ['Disponible', 'Vendido', 'Desactivado', 'Baneado']);
            $table->json('imagenes'); // TODO - Cuidado a la hora de hacer la lógica
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
