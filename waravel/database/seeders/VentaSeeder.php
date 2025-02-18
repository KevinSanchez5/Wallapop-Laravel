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
            'guid' => '0fe3fd18-a0f6-4139-99be-6e1dad2cd420',
            'comprador' => json_encode([
                'id' => 2,
                'guid' => '97b37979-552a-4916-989a-b96031348856',
                'nombre' => 'Maria',
                'apellido' => 'Garcia'
            ]),
            'lineaVentas' => json_encode([
                [
                    'vendedor' => json_encode([
                        'id' => 1,
                        'guid' => 'de6a7d01-af1d-44fb-a615-2583f52da3c4',
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ]),
                    'cantidad' => 2,
                    'producto' => json_encode([
                        'id' => 1,
                        'guid' => '1f0368b0-5ce9-4099-bd13-0c1cede8d349',
                        'nombre' => 'Portatil Gamer',
                        'descripcion' => 'Portatil gaming de gama alta para trabajos pesados.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 800.00,
                        'categoria' => 'Tecnologia'
                    ]),
                    'precioTotal' => 2 * 800.00, // cantidad * precio
                ]
            ]),
            'precioTotal' => 1600.00, // suma de precioTotal de lineas de venta
            'created_at' => now()
        ]);
        Venta::create([
            'guid' => '8345f27b-20b0-4a14-9889-58797c991925',
            'comprador' => json_encode([
                'id' => 3,
                'guid' => '81f58779-2a53-4922-b628-16c330d4b28a',
                'nombre' => 'Pedro',
                'apellido' => 'Martinez'
            ]),
            'lineaVentas' => json_encode([
                [
                    'vendedor' => json_encode([
                        'id' => 1,
                        'guid' => 'de6a7d01-af1d-44fb-a615-2583f52da3c4',
                        'nombre' => 'Juan',
                        'apellido' => 'Perez'
                    ]),
                    'cantidad' => 1,
                    'producto' => json_encode([
                        'id' => 4,
                        'guid' => 'f047b42f-5151-4497-a300-7e78794c850f',
                        'nombre' => 'Pantalones de lana',
                        'descripcion' => 'Pantalones de lana de manga corta y cómodos.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 10.00,
                        'categoria' => 'Ropa',
                    ]),
                    'precioTotal' => 1 * 10.00 // cantidad * precio
                ],
                [
                    'vendedor' => json_encode([
                        'id' => 2,
                        'guid' => '97b37979-552a-4916-989a-b96031348856',
                        'nombre' => 'Maria',
                        'apellido' => 'Garcia'
                    ]),
                    'cantidad' => 2,
                    'producto' => json_encode([
                        'id' => 5,
                        'guid' => 'b060d2c6-9386-46a7-88c8-607850d75585',
                        'nombre' => 'Mario Party 8',
                        'descripcion' => 'Juego de plataformas y acción, muy popular en Nintendo.',
                        'estadoFisico' => 'Nuevo',
                        'precio' => 50.00,
                        'categoria' => 'Videojuegos',
                    ]),
                    'precioTotal' => 2 * 50.00 // cantidad * precio
                ]
            ]),
            'precioTotal' => 110.00, // suma de precioTotal de lineas de venta
            'created_at' => now()
        ]);
    }
}
