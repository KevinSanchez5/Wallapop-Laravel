<?php

namespace Database\Seeders;

use App\Models\Valoracion;
use Illuminate\Database\Seeder;

class ValoracionesSeeder extends Seeder
{
    public function run(): void
    {
        Valoracion::create([
            'guid' => "1a2b3c4d-1234-5678-9abc-def012345678",
            'comentario' => 'Excelente vendedor, muy amable.',
            'puntuacion' => 5,
            'clienteValorado_id' => 2,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "2b3c4d5e-2345-6789-abcd-ef1234567890",
            'comentario' => 'El producto llegó en buen estado, recomendado.',
            'puntuacion' => 4,
            'clienteValorado_id' => 3,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "3c4d5e6f-3456-789a-bcde-f23456789012",
            'comentario' => 'No me gustó el trato, esperaba más comunicación.',
            'puntuacion' => 2,
            'clienteValorado_id' => 1,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "4d5e6f7g-4567-89ab-cdef-345678901234",
            'comentario' => 'Muy buen servicio, repetiré compra.',
            'puntuacion' => 5,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "5e6f7g8h-5678-9abc-def0-456789012345",
            'comentario' => 'Producto con detalles, pero buen vendedor.',
            'puntuacion' => 3,
            'clienteValorado_id' => 2,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
