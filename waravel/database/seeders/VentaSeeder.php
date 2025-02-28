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
            'guid'=>'kY8XqT5L9v3',
            'comprador' => [
                'guid'=>'DU6jCZtareb',
                'id' => 2,
                'nombre' => 'Maria',
                'apellido' => 'Garcia'
            ],
            'estado' =>'',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'guid'=>'G4YXT9K5QLV',
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
            'created_at' => now()
        ]);
        Venta::create([
            'guid'=>'Z4mT7pQX2Vy',
            'comprador' => [
                'guid'=>'yEC3KBt6CFY',
                'id' => 3,
                'nombre' => 'Pedro',
                'apellido' => 'Martinez'
            ],
            'estado' =>'',
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid'=>'2G6HueqixE5',
                        'id' => 1,
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid'=>'VYQ8XK4T9L5',
                        'id' => 4,
                        'nombre' => 'Pantalones Vaqueros',
                        'descripcion' => 'Pantalones Vaqueros cómodos.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 10.00,
                        'categoria' => 'Ropa',
                    ],
                    'precioTotal' => 1 * 10.00
                ],
                [
                    'vendedor' => [
                        'guid'=>'DU6jCZtareb',
                        'id' => 2,
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ],
                    'cantidad' => 2,
                    'producto' => [
                        'guid'=>'T3K9QLYV7X5',
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
            'created_at' => now()
        ]);
    }
}
