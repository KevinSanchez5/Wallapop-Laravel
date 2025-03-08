<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Utils\GuidGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Fábrica para la creación de instancias de la clase Producto.
 *
 * Esta clase se utiliza para generar datos falsos (factores de pruebas) de manera automatizada
 * en entornos de desarrollo o pruebas, para crear registros de productos de forma rápida.
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    /**
     * Define los valores predeterminados para la creación de un nuevo Producto.
     *
     * Utiliza la biblioteca Faker para generar datos aleatorios y llenarlos en un array
     * para crear instancias de la clase `Producto`. Los valores generados son adecuados
     * para pruebas en bases de datos y son reutilizables durante la creación de múltiples instancias.
     *
     * @return array<string, mixed> Un array asociativo con los valores para los atributos del modelo.
     */
    public function definition()
    {
        $vendedor = Cliente::factory()->create();
        return [
            'guid' => $this->faker->bothify('?#?#?#?#?#?'),
            'vendedor_id' => $vendedor->id,
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'estadoFisico' => 'Nuevo',
            'precio' => $this->faker->randomFloat(2, 10, 500),
            'categoria' => 'Videojuegos',
            'estado' => 'Disponible',
            'stock' => $this->faker->numberBetween(1,100),
            'imagenes' => json_encode([
                'https://via.placeholder.com/640x480.png',
                'https://via.placeholder.com/640x480.png'
            ])
        ];
    }
}
