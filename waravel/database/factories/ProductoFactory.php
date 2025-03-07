<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Utils\GuidGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

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
