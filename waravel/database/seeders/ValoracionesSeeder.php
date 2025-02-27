<?php

namespace Database\Seeders;

use App\Models\Valoracion;
use Illuminate\Database\Seeder;

class ValoracionesSeeder extends Seeder
{
    public function run(): void
    {
        Valoracion::create([
            'comentario' => 'Excelente vendedor, muy amable.',
            'puntuacion' => 5,
            'clienteValorado_id' => 2,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'El producto llegó en buen estado, recomendado.',
            'puntuacion' => 4,
            'clienteValorado_id' => 3,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'No me gustó el trato, esperaba más comunicación.',
            'puntuacion' => 2,
            'clienteValorado_id' => 1,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'Muy buen servicio, repetiré compra.',
            'puntuacion' => 5,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'Producto con detalles, pero buen vendedor.',
            'puntuacion' => 3,
            'clienteValorado_id' => 2,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'Excelente trato, todo llegó a tiempo.',
            'puntuacion' => 5,
            'clienteValorado_id' => 1,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'Muy bien, aunque la calidad podría mejorar.',
            'puntuacion' => 3,
            'clienteValorado_id' => 3,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'Gran producto, pero el embalaje no fue el mejor.',
            'puntuacion' => 4,
            'clienteValorado_id' => 1,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'Todo perfecto, muy recomendado.',
            'puntuacion' => 5,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'El producto llegó tarde, pero en buen estado.',
            'puntuacion' => 3,
            'clienteValorado_id' => 3,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'Muy satisfecho con la compra, atención excelente.',
            'puntuacion' => 5,
            'clienteValorado_id' => 1,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'La calidad es buena, pero me hubiera gustado más variedad.',
            'puntuacion' => 4,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'Recibí el producto con un pequeño defecto, pero me lo solucionaron rápido.',
            'puntuacion' => 4,
            'clienteValorado_id' => 3,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'Producto excelente, aunque no es lo que esperaba.',
            'puntuacion' => 1,
            'clienteValorado_id' => 1,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'comentario' => 'Todo llegó a tiempo y en perfectas condiciones.',
            'puntuacion' => 1,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
