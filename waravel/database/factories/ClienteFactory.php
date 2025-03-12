<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Fábrica para la creación de instancias de la clase Cliente.
 *
 * Esta clase se utiliza para generar datos falsos (factores de pruebas) de manera automatizada
 * en entornos de desarrollo o pruebas, para crear registros de clientes de forma rápida.
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    /**
     * Define los valores predeterminados para la creación de un nuevo Cliente.
     *
     * Utiliza la biblioteca Faker para generar datos aleatorios y llenarlos en un array
     * para crear instancias de la clase `Cliente`. Los valores generados son adecuados
     * para pruebas en bases de datos y son reutilizables durante la creación de múltiples instancias.
     *
     * @return array<string, mixed> Un array asociativo con los valores para los atributos del modelo.
     */
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
