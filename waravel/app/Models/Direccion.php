<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

/**
 * Clase Direccion que maneja la conversión de direcciones a un objeto y viceversa.
 *
 * Esta clase implementa la interfaz `CastsAttributes` de Eloquent para permitir la conversión
 * de datos JSON a un objeto `Direccion` y la conversión del objeto `Direccion` a JSON para su almacenamiento.
 */

class Direccion implements CastsAttributes
{
    public string $calle;
    public int $numero;
    public int $piso;
    public string $letra;
    public int $codigoPostal;

    /**
     * Constructor de la clase Dirección.
     *
     * Inicializa los atributos de la dirección.
     *
     * @param string $calle El nombre de la calle.
     * @param int $numero El número de la calle.
     * @param int $piso El piso del edificio.
     * @param string $letra La letra del piso.
     * @param int $codigoPostal El código postal de la dirección.
     */

    public function __construct(string $calle, int $numero, int $piso, string $letra, int $codigoPostal)
    {
        $this->calle = $calle;
        $this->numero = $numero;
        $this->piso = $piso;
        $this->letra = $letra;
        $this->codigoPostal = $codigoPostal;
    }

    /**
     * Método para convertir un valor JSON desde la base de datos a un objeto Direccion.
     *
     * Este método es utilizado por Eloquent cuando se accede a una columna que está
     * configurada para ser convertida a un objeto de esta clase.
     *
     * @param mixed $model El modelo que contiene la columna.
     * @param string $key La clave de la columna.
     * @param mixed $value El valor almacenado en la base de datos.
     * @param array $attributes Los atributos del modelo.
     *
     * @return \App\Models\Direccion Un objeto Direccion con los datos deserializados.
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $data = json_decode($value, true);

        return new self(
            $data['calle'] ?? '',
            $data['numero'] ?? 0,
            $data['$piso'] ?? 0,
            $data['$letra'] ?? '',
            $data['$codigoPostal'] ?? 0
        );
    }

    /**
     * Método para convertir un objeto Direccion a JSON antes de guardarlo en la base de datos.
     *
     * Este método es utilizado por Eloquent cuando se guarda un modelo que tiene
     * un atributo con una clase personalizada como `Direccion`.
     *
     * @param mixed $model El modelo que contiene el atributo.
     * @param string $key La clave del atributo.
     * @param \App\Models\Direccion $value El objeto Direccion a convertir.
     * @param array $attributes Los atributos del modelo.
     *
     * @return string El valor JSON que se almacenará en la base de datos.
     *
     * @throws \InvalidArgumentException Si el valor no es una instancia de la clase Direccion.
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (!$value instanceof self) {
            throw new InvalidArgumentException("El valor debe ser una instancia de Direccion.");
        }

        return json_encode([
            'calle' => $value->calle,
            'numero' => $value->numero,
            'ciudad' => $value->ciudad,
            'pais' => $value->pais,
            'codigoPostal' => $value->codigoPostal,
        ]);
    }
}
