<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

class VentaFactory extends Factory
{
    protected $model = Venta::class;

    public function definition()
    {
        $lineaVentas = [
            [
                'producto_id' => Producto::factory(),
                'cantidad' => $this->faker->numberBetween(1, 5),
                'precio' => $this->faker->randomFloat(2, 10, 100),
            ],
        ];

        $precioTotal = array_reduce($lineaVentas, function ($carry, $item) {
            return $carry + ($item['precio'] * $item['cantidad']);
        }, 0);

        return [
            'guid' => $this->faker->uuid,
            'comprador' => json_encode([
                'nombre' => $this->faker->name,
                'email' => $this->faker->email,
            ]),
            'lineaVentas' => json_encode($lineaVentas),
            'precioTotal' => $precioTotal,
        ];
    }
}
