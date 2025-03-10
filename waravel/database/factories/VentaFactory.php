<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Venta;
use App\Utils\GuidGenerator;
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
        $producto = Producto::factory()->create(); // Crea un producto para la venta

        return [
            'guid' => GuidGenerator::GenerarId(),
            'estado' => $this->faker->randomElement(['Pendiente', 'Procesando', 'Enviado', 'Entregado', 'Cancelado']),
            'comprador' => [
                'guid' => GuidGenerator::GenerarId(),
                'id' => $this->faker->randomNumber(),
                'nombre' => $this->faker->firstName,
                'apellido' => $this->faker->lastName,
            ],
            'lineaVentas' => [
                [
                    'vendedor' => [
                        'guid' => GuidGenerator::GenerarId(),
                        'id' => $this->faker->randomNumber(),
                        'nombre' => $this->faker->firstName,
                        'apellido' => $this->faker->lastName,
                    ],
                    'cantidad' => 1,
                    'producto' => [
                        'guid' => GuidGenerator::GenerarId(),
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'imagenes' => [$this->faker->imageUrl()],
                        'descripcion' => $producto->descripcion,
                        'estadoFisico' => $this->faker->randomElement(['Nuevo', 'Usado']),
                        'precio' => $producto->precio,
                        'categoria' => $producto->categoria,
                    ],
                    'precioTotal' => $producto->precio * 1,
                ],
            ],
            'precioTotal' => $producto->precio * 1,
        ];
    }
}
