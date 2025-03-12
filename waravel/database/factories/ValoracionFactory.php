<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Valoracion;
use App\Utils\GuidGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Fábrica para la creación de instancias de la clase Valoracion.
 *
 * Esta clase se utiliza para generar datos falsos (factores de pruebas) de manera automatizada
 * en entornos de desarrollo o pruebas, para crear registros de valoraciones de productos o servicios.
 */
class ValoracionFactory extends Factory
{
    protected $model = Valoracion::class;

    /**
     * Define los valores predeterminados para la creación de una nueva Valoración.
     *
     * Utiliza la biblioteca Faker para generar datos aleatorios y llenarlos en un array
     * para crear instancias de la clase `Valoracion`. Los valores generados son adecuados
     * para pruebas en bases de datos y son reutilizables durante la creación de múltiples instancias.
     *
     * @return array<string, mixed> Un array asociativo con los valores para los atributos del modelo.
     */
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
