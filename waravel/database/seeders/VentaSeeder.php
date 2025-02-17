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
                'nombre' => 'María García'
            ]),
            'lineaVentas' => json_encode([
                [
                    'id' => 1,
                    'guid' => 'ac81bc2d-df46-492e-80a9-5616654f276e',
                    'vendedor' => json_encode([
                        'id' => 1,
                        'nombre' => 'Juan Pérez'
                    ]),
                    'cantidad' => 2,
                    'producto' => json_encode([
                        'id' => 1,
                        'nombre' => 'Portatil Gamer',
                        'descripcion' => 'Portatil potente para gaming y trabajo pesado.'
                    ]),
                    'precio' => 800.00,
                    'precioTotal' => 1 * 800.00,
                ]
            ]),
            'precioTotal' => 1 * 800.00,
        ]);
        Venta::create([
            'guid' => '8345f27b-20b0-4a14-9889-58797c991925',
            'comprador' => json_encode([
                'id' => 3,
                'nombre' => 'Pedro Martínez'
            ]),
            'lineaVentas' => json_encode([
                [
                    'id' => 2,
                    'guid' => '73372775-4557-4324-8167-3c9a5b5684a5',
                    'vendedor' => json_encode([
                        'id' => 1,
                        'nombre' => 'Juan Pérez'
                    ]),
                    'cantidad' => 1,
                    'producto' => json_encode([
                        'id' => 4,
                        'nombre' => 'Pantalones de lana',
                        'descripcion' => 'Pantalones de lana de manga corta y cómodos.'
                    ]),
                    'precio' => 10.00,
                    'precioTotal' => 1 * 10.00,
                ]
            ]),
            'precioTotal' => 1 * 10.00,
        ]);
    }
}
