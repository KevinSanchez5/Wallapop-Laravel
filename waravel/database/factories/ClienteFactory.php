<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Direccion;
use App\Models\User;
use App\Utils\GuidGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition()
    {
        return [
            'guid' => GuidGenerator::generarId(),
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'avatar' => $this->faker->imageUrl(),
            'telefono' => $this->faker->phoneNumber,
            'direccion' => [
                'calle' => $this->faker->streetAddress,
                'numero' => $this->faker->numberBetween(100, 999),
                'piso' => $this->faker->numberBetween(-1, 20),
                'letra' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
                'codigoPostal' => 28001
            ],
            'activo' => $this->faker->boolean,
            'usuario_id' => User::factory(),
        ];
    }
}
