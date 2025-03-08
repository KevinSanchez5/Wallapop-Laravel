<?php

namespace App\Models;


/**
 * Clase Carrito que representa un carrito de compras.
 *
 * Esta clase es utilizada para gestionar las líneas de productos dentro de un carrito de compras,
 * el precio total de los productos y la cantidad total de artículos.
 */
class Carrito
{
    /**
     * @var array $lineasCarrito Almacena las líneas de productos del carrito.
     * Cada línea representa un producto en el carrito.
     */
    public $lineasCarrito;
    /**
     * @var float $precioTotal El precio total de los productos en el carrito.
     */
    public $precioTotal;
    /**
     * @var int $itemAmount La cantidad total de productos en el carrito.
     */
    public $itemAmount;

    /**
     * Constructor de la clase Carrito.
     *
     * Este constructor inicializa el carrito con los atributos proporcionados. Si no se proporcionan
     * valores para las propiedades, se asignan valores predeterminados.
     *
     * @param array $attributes Atributos opcionales para inicializar las propiedades del carrito.
     * @param array $attributes['lineasCarrito'] Línea de productos en el carrito.
     * @param float $attributes['precioTotal'] El precio total de los productos en el carrito.
     * @param int $attributes['itemAmount'] La cantidad total de productos en el carrito.
     */

    public function __construct($attributes = [])
    {
        $this->lineasCarrito = $attributes['lineasCarrito'] ?? [];
        $this->precioTotal = $attributes['precioTotal'] ?? 0;
        $this->itemAmount = $attributes['itemAmount'] ?? 0;
    }
    /**
     * Convierte el objeto Carrito en una cadena de texto.
     *
     * Esta función se usa cuando se necesita representar el carrito como una cadena de texto,
     * proporcionando una descripción de las líneas del carrito, el precio total y la cantidad de productos.
     *
     * @return string Una representación en cadena de texto del carrito con sus líneas, precio total y cantidad.
     */

    public function __toString()
    {
        $lineas = array_map(function($linea) {
            return (string)$linea;
        }, $this->lineasCarrito);

        $lineasString = implode(", ", $lineas);
        return "Lineas: [$lineasString], Precio Total: {$this->precioTotal}, Cantidad de productos: {$this->itemAmount}";
    }
}
