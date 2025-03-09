<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Venta;
use App\Utils\GuidGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Guid\Guid;

class VentaFactory extends Factory
{
    protected $model = Venta::class;

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
