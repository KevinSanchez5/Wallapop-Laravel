<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Producto;
use App\Utils\GuidGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition()
    {
        $cliente = Cliente::factory()->create();

        return [
            'guid' => GuidGenerator::generarId(),
            'vendedor_id' => $cliente->id,
            'nombre' => $this->faker->word,
            'descripcion' => $this->faker->sentence,
            'estadoFisico' => $this->faker->randomElement(['Nuevo', 'Usado', 'Deteriorado']),
            'precio' => $this->faker->randomFloat(2, 10, 1000),
            'categoria' => $this->faker->randomElement(['Tecnologia', 'Ropa', 'Hogar', 'Coleccionismo', 'Vehiculos', 'Videojuegos', 'Musica', 'Deporte', 'Cine', 'Cocina']),
            'estado' => $this->faker->randomElement(['Disponible', 'Vendido', 'Desactivado']),
            'stock' => $this->faker->numberBetween(1, 100),
            'imagenes' => json_encode([$this->faker->imageUrl(), $this->faker->imageUrl()]),
        ];
    }
}
