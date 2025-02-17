<?php

namespace Database\Seeders;

use App\Models\LineaCarrito;
use Illuminate\Database\Seeder;

class LineaCarritoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LineaCarrito::create([
            'guid' => '73c96fa5-cc42-4e75-b20a-0d1030dc197a',
            'producto' => json_encode([
                'id' => 4,
                'nombre' => 'Pantalones de lana',
                'descripcion' => 'Pantalones de lana de manga corta y cómodos.'
            ]),
            'cantidad' => 1,
            'precioTotal' => 1 * 10.00,
        ]);
    }
}
