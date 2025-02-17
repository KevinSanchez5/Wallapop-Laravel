<?php

namespace Database\Seeders;

use App\Models\Carrito;
use Illuminate\Database\Seeder;

class CarritoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Carrito::create([
            'guid' => '12345-abcde-67890-fghij',
            'cliente' => json_encode([
                'nombre' => 'Pedro',
                'apellido' => 'Martínez',
                'telefono' => '321456789'
            ]),
            'lineaCarrito' => json_encode([
                [
                    'producto_id' => 1,
                    'cantidad' => 1,
                    'precio' => 10.00
                ]
            ]),
            'precioTotal' => 10.00
        ]);
    }
}
