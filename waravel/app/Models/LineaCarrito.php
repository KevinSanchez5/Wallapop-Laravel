<?php

namespace App\Models;

class LineaCarrito {
    public $producto;
    public $cantidad;
    public $precioTotal;

    public function __construct($attributes = []) {
        $this->producto = $attributes['producto'] ?? null;
        $this->cantidad = $attributes['cantidad'] ?? 1;
        $this->precioTotal = $attributes['precioTotal'] ?? 0;
    }

    public function __toString()
    {
        return "Producto: {$this->producto->nombre}, Cantidad: {$this->cantidad}, Total: {$this->precioTotal}";
    }

}
