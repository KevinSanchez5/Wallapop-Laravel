<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\User;
use App\Models\Valoracion;
use Faker\Factory;

class ValoracionFactory extends Factory
{
    protected $model = Valoracion::class;
    private $faker;

    public function definition()
    {
        return [
            'producto_id' => Producto::factory(),
            'user_id' => User::factory(),
            'puntuacion' => $this->faker->numberBetween(1, 5),
            'comentario' => $this->faker->sentence(),
        ];
    }
}
