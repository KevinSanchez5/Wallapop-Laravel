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
            'guid' => $this->faker->bothify('?#?#?#?#?#?'),
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'avatar' => 'clientes/avatar.png',
            'telefono' => '612345678',
            'direccion' => [
                'calle' => 'Avenida Siempre Viva',
                'numero' => 742,
                'piso' => 1,
                'letra' => 'A',
                'codigoPostal' => 28001
            ],
            'activo' => 'true',
            'usuario_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
