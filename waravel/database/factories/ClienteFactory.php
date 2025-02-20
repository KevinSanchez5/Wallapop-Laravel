<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition()
    {
        return [
            'guid' => $this->faker->uuid,
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'avatar' => $this->faker->imageUrl(),
            'telefono' => $this->faker->phoneNumber,
            'direccion' => json_encode([
                'calle' => $this->faker->streetAddress,
                'ciudad' => $this->faker->city,
                'pais' => $this->faker->country,
            ]),
            'activo' => $this->faker->boolean,
            'usuario_id' => User::factory(),
        ];
    }
}
