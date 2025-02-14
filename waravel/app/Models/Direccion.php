<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Direccion implements CastsAttributes
{
    public string $calle;
    public int $numero;
    public int $piso;
    public string $letra;
    public int $codigoPostal;

    public function __construct(string $calle, int $numero, int $piso, string $letra, int $codigoPostal)
    {
        $this->calle = $calle;
        $this->numero = $numero;
        $this->piso = $piso;
        $this->letra = $letra;
        $this->codigoPostal = $codigoPostal;
    }

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
