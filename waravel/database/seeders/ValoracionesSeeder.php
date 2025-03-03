<?php

namespace Database\Seeders;

use App\Models\Valoracion;
use Illuminate\Database\Seeder;

class ValoracionesSeeder extends Seeder
{
    public function run(): void
    {
        $valoraciones = [
            ['guid' => 'G7pXkT3L9qY', 'comentario' => 'Excelente vendedor, muy amable.', 'puntuacion' => 5, 'clienteValorado_id' => 2, 'autor_id' => 3,],
            ['guid' => 'Y5qT2X9L8mV', 'comentario' => 'Producto excelente, aunque no es lo que esperaba.', 'puntuacion' => 1, 'clienteValorado_id' => 1, 'autor_id' => 3,],
            ['guid' => 'vT5QmX2L8pZ', 'comentario' => 'El producto llegó en buen estado, recomendado.', 'puntuacion' => 4, 'clienteValorado_id' => 3, 'autor_id' => 1,],
            ['guid' => 'Y9XqT4L7mV3', 'comentario' => 'No me gustó el trato, esperaba más comunicación.', 'puntuacion' => 2, 'clienteValorado_id' => 1, 'autor_id' => 2,],
            ['guid' => 'L8mT5X7pY9q', 'comentario' => 'Todo llegó a tiempo y en perfectas condiciones.', 'puntuacion' => 1, 'clienteValorado_id' => 2, 'autor_id' => 1,],
            ['guid' => 'T9XqY5L3pV7', 'comentario' => 'Excelente trato, todo llegó a tiempo.', 'puntuacion' => 5, 'clienteValorado_id' => 1, 'autor_id' => 2,],
            ['guid' => 'X2mT7L9pQY5', 'comentario' => 'Producto con detalles, pero buen vendedor.', 'puntuacion' => 3, 'clienteValorado_id' => 2, 'autor_id' => 3,],
            ['guid' => 'L3T8pX5YqZ7', 'comentario' => 'Muy buen servicio, repetiré compra.', 'puntuacion' => 5, 'clienteValorado_id' => 2, 'autor_id' => 1,],
            ['guid' => 'V9pX5T3L7qY', 'comentario' => 'Muy satisfecho con la compra, atención excelente.', 'puntuacion' => 5, 'clienteValorado_id' => 1, 'autor_id' => 3,],
            ['guid' => 'Z4pT8mX2L7Y', 'comentario' => 'Muy bien, aunque la calidad podría mejorar.', 'puntuacion' => 3, 'clienteValorado_id' => 3, 'autor_id' => 1],
            ['guid' => 'qX7T5L9mY8V', 'comentario' => 'Gran producto, pero el embalaje no fue el mejor.', 'puntuacion' => 4, 'clienteValorado_id' => 1, 'autor_id' => 3,],
            ['guid' => 'L5mT3pX9qY7', 'comentario' => 'Todo perfecto, muy recomendado.', 'puntuacion' => 5, 'clienteValorado_id' => 2, 'autor_id' => 1],
            ['guid' => 'X8qT7L2mY9V', 'comentario' => 'El producto llegó tarde, pero en buen estado.', 'puntuacion' => 3, 'clienteValorado_id' => 3, 'autor_id' => 2],
            ['guid' => 'L2XqT8mY7pV', 'comentario' => 'La calidad es buena, pero me hubiera gustado más variedad.', 'puntuacion' => 4, 'clienteValorado_id' => 2, 'autor_id' => 1],
            ['guid' => 'X7T9L3pY5mV', 'comentario' => 'Recibí el producto con un pequeño defecto, pero me lo solucionaron rápido.', 'puntuacion' => 4, 'clienteValorado_id' => 3, 'autor_id' => 2],
            ['guid' => 'A1B2C3Dr4E5', 'comentario' => 'Servicio excepcional, muy recomendable.', 'puntuacion' => 5, 'clienteValorado_id' => 1, 'autor_id' => 2],
            ['guid' => 'F6G7H8I9rJ0', 'comentario' => 'Entrega rápida, aunque el embalaje podría mejorar.', 'puntuacion' => 4, 'clienteValorado_id' => 2, 'autor_id' => 3],
            ['guid' => 'Kr1L2M3N4O5', 'comentario' => 'Producto en perfecto estado, muy contento.', 'puntuacion' => 5, 'clienteValorado_id' => 3, 'autor_id' => 4],
            ['guid' => 'P6rQ7R8S9T0', 'comentario' => 'Atención al cliente muy buena, repetiré.', 'puntuacion' => 5, 'clienteValorado_id' => 4, 'autor_id' => 5],
            ['guid' => 'U1Vr2W3X4Y5', 'comentario' => 'Hubo un retraso, pero se solucionó rápido.', 'puntuacion' => 3, 'clienteValorado_id' => 5, 'autor_id' => 6],
            ['guid' => 'Z6A7rB8C9D0', 'comentario' => 'No me gustó el trato, esperaba más comunicación.', 'puntuacion' => 2, 'clienteValorado_id' => 6, 'autor_id' => 7],
            ['guid' => 'E1F2Gr3H4I5', 'comentario' => 'Muy atento el vendedor, calidad buena.', 'puntuacion' => 4, 'clienteValorado_id' => 7, 'autor_id' => 8],
            ['guid' => 'J6K7L8rM9N0', 'comentario' => 'Producto con defectos, esperaba más.', 'puntuacion' => 2, 'clienteValorado_id' => 8, 'autor_id' => 9],
            ['guid' => 'O1P2Q3Rr4S5', 'comentario' => 'Entrega puntual y en perfectas condiciones.', 'puntuacion' => 5, 'clienteValorado_id' => 9, 'autor_id' => 10],
            ['guid' => 'T6U7V8W9rX0', 'comentario' => 'El precio es justo para la calidad recibida.', 'puntuacion' => 4, 'clienteValorado_id' => 10, 'autor_id' => 11],
            ['guid' => 'Y1Z2rA3B4C5', 'comentario' => 'Atención amable, aunque algo demorada.', 'puntuacion' => 3, 'clienteValorado_id' => 11, 'autor_id' => 12],
            ['guid' => 'D6rE7F8G9H0', 'comentario' => 'Calidad excepcional, repetiré sin duda.', 'puntuacion' => 5, 'clienteValorado_id' => 12, 'autor_id' => 1],
            ['guid' => 'Ir1J2K3L4M5', 'comentario' => 'No me gustó el producto, pero me lo solucionaron rápido.', 'puntuacion' => 2, 'clienteValorado_id' => 1, 'autor_id' => 11],
            ['guid' => 'N6Or7P8Q9R0', 'comentario' => 'Muy atento al cliente, calidad buena.', 'puntuacion' => 4, 'clienteValorado_id' => 2, 'autor_id' => 12],
            ['guid' => 'Sr1T2U3V4W5', 'comentario' => 'El precio es justo para la calidad recibida.', 'puntuacion' => 4, 'clienteValorado_id' => 3, 'autor_id' => 1],
            ['guid' => 'X6rY7Z8A9B0', 'comentario' => 'No me gustó el producto, pero me lo solucionaron rápido.', 'puntuacion' => 2, 'clienteValorado_id' => 4, 'autor_id' => 2],
            ['guid' => 'C7Dr8E9F0G1', 'comentario' => 'Entrega puntual y en perfectas condiciones.', 'puntuacion' => 5, 'clienteValorado_id' => 5, 'autor_id' => 7],
            ['guid' => 'H8I9rJ0K1L2', 'comentario' => 'Calidad excepcional, repetiré sin duda.', 'puntuacion' => 5, 'clienteValorado_id' => 6, 'autor_id' => 8],
            ['guid' => 'M9N0Or1P2Q3', 'comentario' => 'No me gustó el producto, pero me lo solucionaron rápido.', 'puntuacion' => 2, 'clienteValorado_id' => 7, 'autor_id' => 9],
            ['guid' => 'R0S1T2rU3V4', 'comentario' => 'Entrega puntual y en perfectas condiciones.', 'puntuacion' => 5, 'clienteValorado_id' => 8, 'autor_id' => 2],
            ['guid' => 'X5Y6Z7A48B9', 'comentario' => 'No me gustó el producto, pero me lo solucionaron rápido.', 'puntuacion' => 2, 'clienteValorado_id' => 9, 'autor_id' => 10],
            ['guid' => 'E6fF7G8H9I0', 'comentario' => 'Entrega puntual y en perfectas condiciones.', 'puntuacion' => 5, 'clienteValorado_id' => 10, 'autor_id' => 11],
            ['guid' => 'Av7B8C9D0E1', 'comentario' => 'No me gustó el producto, pero me lo solucionaron rápido.', 'puntuacion' => 2, 'clienteValorado_id' => 11, 'autor_id' => 1],
            ['guid' => 'F8Gp9H0I1J2', 'comentario' => 'Entrega puntual y en perfectas condiciones.', 'puntuacion' => 5, 'clienteValorado_id' => 12, 'autor_id' => 10],
            ['guid' => 'L9M0aN1O2P3', 'comentario' => 'No me gustó el producto, pero me lo solucionaron rápido.', 'puntuacion' => 2, 'clienteValorado_id' => 1, 'autor_id' => 12],
            ['guid' => 'Q4R5S61T7U8', 'comentario' => 'No me gustó el producto, pero me lo solucionaron rápido.', 'puntuacion' => 2, 'clienteValorado_id' => 2, 'autor_id' => 1],
            ['guid' => 'W9X0Y1Z32A3', 'comentario' => 'No me gustó el producto, pero me lo solucionaron rápido.', 'puntuacion' => 2, 'clienteValorado_id' => 3, 'autor_id' => 11],
            ['guid' => 'E0F1G2H3kI4', 'comentario' => 'No me gustó el producto, pero me lo solucionaron rápido.', 'puntuacion' => 2, 'clienteValorado_id' => 4, 'autor_id' => 1],
            ['guid' => 'qX7T5j9mY8V', 'comentario' => 'Gran producto, pero el embalaje no fue el mejor.', 'puntuacion' => 4, 'clienteValorado_id' => 1, 'autor_id' => 3,],
            ['guid' => 'L5mT3lX9qY7', 'comentario' => 'Todo perfecto, muy recomendado.', 'puntuacion' => 1, 'clienteValorado_id' => 2, 'autor_id' => 1],
            ['guid' => 'X8qT7f2mY9V', 'comentario' => 'El producto llegó tarde, pero en buen estado.', 'puntuacion' => 3, 'clienteValorado_id' => 3, 'autor_id' => 2],
            ['guid' => 'L2XqTamY7pV', 'comentario' => 'La calidad es buena, pero me hubiera gustado más variedad.', 'puntuacion' => 4, 'clienteValorado_id' => 2, 'autor_id' => 1],
            ['guid' => 'X7T9LdpY5mV', 'comentario' => 'Recibí el producto con un pequeño defecto, pero me lo solucionaron rápido.', 'puntuacion' => 4, 'clienteValorado_id' => 3, 'autor_id' => 2],
            ['guid' => 'A1B0C7Dr4E5', 'comentario' => 'Servicio excepcional, muy recomendable.', 'puntuacion' => 1, 'clienteValorado_id' => 1, 'autor_id' => 2],
            ['guid' => 'F6G7H8u9rJ0', 'comentario' => 'Entrega rápida, aunque el embalaje podría mejorar.', 'puntuacion' => 4, 'clienteValorado_id' => 2, 'autor_id' => 3],

        ];

        foreach ($valoraciones as $valoracion) {
            Valoracion::create(array_merge($valoracion, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
