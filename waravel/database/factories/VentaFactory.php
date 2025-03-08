<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Fábrica para la creación de instancias de la clase Venta.
 *
 * Esta clase se utiliza para generar datos falsos (factores de pruebas) en entornos de desarrollo o pruebas,
 * para crear registros de ventas en la base de datos de forma automatizada.
 */
class VentaFactory extends Factory
{
    protected $model = Venta::class;

    /**
     * Define los valores predeterminados para la creación de una nueva Venta.
     *
     * Utiliza la biblioteca Faker para generar datos aleatorios y llenar los atributos
     * de la clase `Venta`. Los valores generados son adecuados para pruebas en bases de datos
     * y pueden ser reutilizados durante la creación de múltiples instancias.
     *
     * @return array<string, mixed> Un array asociativo con los valores para los atributos del modelo.
     */
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
