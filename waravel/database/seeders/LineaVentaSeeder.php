<?php

namespace Database\Seeders;

use App\Models\LineaVenta;
use Illuminate\Database\Seeder;

class LineaVentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LineaVenta::create([
            'guid' => 'ac81bc2d-df46-492e-80a9-5616654f276e',
            'vendedor' => json_encode([
                'id' => 1,
                'nombre' => 'Juan Pérez'
            ]),
            'cantidad' => 1,
            'producto' => json_encode([
                'id' => 1,
                'nombre' => 'Portatil Gamer',
                'descripcion' => 'Portatil potente para gaming y trabajo pesado.'
            ]),
            'precio' => 800.00,
            'precioTotal' => 1 * 800.00,
        ]);
        LineaVenta::create([
            'guid' => 'd79b506f-583d-42d3-a788-789224d80b58',
            'vendedor' => json_encode([
                'id' => 2,
                'nombre' => 'María García'
            ]),
            'cantidad' => 2,
            'producto' => json_encode([
                'id' => 2,
                'nombre' => 'Chaqueta de cuero',
                'descripcion' => 'Chaqueta elegante y resistente, ideal para el frío.'
            ]),
            'precio' => 15.00,
            'precioTotal' => 2 * 15.00,
        ]);
        LineaVenta::create([
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
        ]);
    }
}
