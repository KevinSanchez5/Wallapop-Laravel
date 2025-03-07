<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition()
    {
        return [
            'guid' => Str::uuid(),
            'vendedor_id' => User::factory(),
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'estadoFisico' => 'Nuevo',
            'precio' => $this->faker->randomFloat(2, 10, 500),
            'categoria' => 'Videojuegos',
            'estado' => 'Disponible',
            'imagenes' => json_encode([
                'https://via.placeholder.com/640x480.png',
                'https://via.placeholder.com/640x480.png'
            ])
        ];
    }
}
