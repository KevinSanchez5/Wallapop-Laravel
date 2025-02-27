<?php

namespace Database\Seeders;

use App\Models\Venta;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Venta::create([

            'comprador' => [
                'id' => 2,
                'nombre' => 'Maria',
                'apellido' => 'Garcia'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'id' => 1,
                        'nombre' => 'Portatil Gamer',
                        'descripcion' => 'Portatil gaming de gama alta para trabajos pesados.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 800.00,
                        'categoria' => 'Tecnologia'
                    ],
                    'precioTotal' => 2 * 800.00,
                ]
            ],
            'precioTotal' => 1600.00,
            'estado' => 'Entregado',
            'created_at' => now()
        ]);
        Venta::create([
            'comprador' => [
                'id' => 3,
                'nombre' => 'Pedro',
                'apellido' => 'Martinez'
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'id' => 4,
                        'nombre' => 'Pantalones de lana',
                        'descripcion' => 'Pantalones de lana de manga corta y cómodos.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 10.00,
                        'categoria' => 'Ropa',
                    ],
                    'precioTotal' => 1 * 10.00
                ],
                [
                    'vendedor' => [
                        'id' => 2,
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'id' => 5,
                        'nombre' => 'Mario Party 8',
                        'descripcion' => 'Juego de plataformas y acción, muy popular en Nintendo.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 50.00,
                        'categoria' => 'Videojuegos',
                    ],
                    'precioTotal' => 2 * 50.00
                ]
            ],
            'precioTotal' => 110.00,
            'estado' => 'Entregado',
            'created_at' => now(),
        ]);
    }
}
