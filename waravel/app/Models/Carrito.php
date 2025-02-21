<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;class Carrito
{
    public $lineasCarrito;
    public $precioTotal;

    public function __construct($attributes = [])
    {
        $this->lineasCarrito = $attributes['lineasCarrito'] ?? [];
        $this->precioTotal = $attributes['precioTotal'] ?? 0;
    }

    public function __toString()
    {
        $lineas = array_map(function($linea) {
            return (string)$linea;
        }, $this->lineasCarrito);

        $lineasString = implode(", ", $lineas);
        return "Lineas: [$lineasString], Precio Total: {$this->precioTotal}";
    }
}
