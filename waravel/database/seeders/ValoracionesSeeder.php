<?php

namespace Database\Seeders;

use App\Models\Valoracion;
use Illuminate\Database\Seeder;

class ValoracionesSeeder extends Seeder
{
    public function run(): void
    {
        Valoracion::create([
            'guid' => 'G7pXkT3L9qY',
            'comentario' => 'Excelente vendedor, muy amable.',
            'puntuacion' => 5,
            'clienteValorado_id' => 2,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'vT5QmX2L8pZ',
            'comentario' => 'El producto llegó en buen estado, recomendado.',
            'puntuacion' => 4,
            'clienteValorado_id' => 3,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'Y9XqT4L7mV3',
            'comentario' => 'No me gustó el trato, esperaba más comunicación.',
            'puntuacion' => 2,
            'clienteValorado_id' => 1,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'L3T8pX5YqZ7',
            'comentario' => 'Muy buen servicio, repetiré compra.',
            'puntuacion' => 5,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'X2mT7L9pQY5',
            'comentario' => 'Producto con detalles, pero buen vendedor.',
            'puntuacion' => 3,
            'clienteValorado_id' => 2,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'T9XqY5L3pV7',
            'comentario' => 'Excelente trato, todo llegó a tiempo.',
            'puntuacion' => 5,
            'clienteValorado_id' => 1,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'Z4pT8mX2L7Y',
            'comentario' => 'Muy bien, aunque la calidad podría mejorar.',
            'puntuacion' => 3,
            'clienteValorado_id' => 3,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'qX7T5L9mY8V',
            'comentario' => 'Gran producto, pero el embalaje no fue el mejor.',
            'puntuacion' => 4,
            'clienteValorado_id' => 1,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'L5mT3pX9qY7',
            'comentario' => 'Todo perfecto, muy recomendado.',
            'puntuacion' => 5,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'X8qT7L2mY9V',
            'comentario' => 'El producto llegó tarde, pero en buen estado.',
            'puntuacion' => 3,
            'clienteValorado_id' => 3,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'V9pX5T3L7qY',
            'comentario' => 'Muy satisfecho con la compra, atención excelente.',
            'puntuacion' => 5,
            'clienteValorado_id' => 1,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'L2XqT8mY7pV',
            'comentario' => 'La calidad es buena, pero me hubiera gustado más variedad.',
            'puntuacion' => 4,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'X7T9L3pY5mV',
            'comentario' => 'Recibí el producto con un pequeño defecto, pero me lo solucionaron rápido.',
            'puntuacion' => 4,
            'clienteValorado_id' => 3,
            'autor_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'Y5qT2X9L8mV',
            'comentario' => 'Producto excelente, aunque no es lo que esperaba.',
            'puntuacion' => 1,
            'clienteValorado_id' => 1,
            'autor_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Valoracion::create([
            'guid' => 'L8mT5X7pY9q',
            'comentario' => 'Todo llegó a tiempo y en perfectas condiciones.',
            'puntuacion' => 1,
            'clienteValorado_id' => 2,
            'autor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
