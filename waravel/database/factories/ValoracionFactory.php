<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Valoracion;
use App\Utils\GuidGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValoracionFactory extends Factory
{
    protected $model = Valoracion::class;

    public function definition()
    {
        return [
            'guid' => GuidGenerator::generarId(),
            'comentario' => $this->faker->sentence(),
            'puntuacion' => $this->faker->numberBetween(1, 5),
            'clienteValorado_id' => Cliente::factory()->create(),
            'autor_id' => Cliente::factory()->create(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
