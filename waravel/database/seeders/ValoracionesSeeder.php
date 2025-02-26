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

        // Nuevas valoraciones
        Valoracion::create([
            'guid' => "6f7g8h9i-6789-0abc-def1-567890123456",
            'comentario' => 'Excelente trato, todo llegó a tiempo.',
            'puntuacion' => 5,
            'clienteValorado_id' => 1,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "7g8h9i0j-789a-1bcd-ef23-678901234567",
            'comentario' => 'Muy bien, aunque la calidad podría mejorar.',
            'puntuacion' => 3,
            'clienteValorado_id' => 3,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "8h9i0j1k-890b-2cde-fg34-789012345678",
            'comentario' => 'Gran producto, pero el embalaje no fue el mejor.',
            'puntuacion' => 4,
            'clienteValorado_id' => 1,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "9i0j1k2l-901c-3def-hg45-890123456789",
            'comentario' => 'Todo perfecto, muy recomendado.',
            'puntuacion' => 5,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "10j1k2l3m-012d-4efg-ij56-901234567890",
            'comentario' => 'El producto llegó tarde, pero en buen estado.',
            'puntuacion' => 3,
            'clienteValorado_id' => 3,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Más valoraciones
        Valoracion::create([
            'guid' => "11k2l3m4n-123e-4fgh-kl67-123456789012",
            'comentario' => 'Muy satisfecho con la compra, atención excelente.',
            'puntuacion' => 5,
            'clienteValorado_id' => 1,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "12l3m4n5o-234f-5ghi-mn78-234567890123",
            'comentario' => 'La calidad es buena, pero me hubiera gustado más variedad.',
            'puntuacion' => 4,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "13m4n5o6p-345g-6hij-no89-345678901234",
            'comentario' => 'Recibí el producto con un pequeño defecto, pero me lo solucionaron rápido.',
            'puntuacion' => 4,
            'clienteValorado_id' => 3,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "14n5o6p7q-456h-7ijk-op90-456789012345",
            'comentario' => 'Producto excelente, aunque no es lo que esperaba.',
            'puntuacion' => 1,
            'clienteValorado_id' => 1,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => "15o6p7q8r-567i-8jkl-qp01-567890123456",
            'comentario' => 'Todo llegó a tiempo y en perfectas condiciones.',
            'puntuacion' => 1,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
