<?php

namespace App\Models;

/**
 * Clase LineaCarrito que representa una línea de un carrito de compras.
 *
 * Cada línea contiene información sobre un producto, la cantidad de ese producto en el carrito
 * y el precio total de esa línea de productos.
 */
class LineaCarrito {
    public $producto;
    public $cantidad;
    public $precioTotal;

    /**
     * Constructor de la clase LineaCarrito.
     *
     * Inicializa los atributos de la línea de carrito con los valores proporcionados.
     * Si no se proporciona un valor, se usan los valores por defecto (producto = null, cantidad = 1, precioTotal = 0).
     *
     * @param array $attributes Atributos para inicializar la línea de carrito.
     *      - 'producto' (Producto): El producto asociado con esta línea.
     *      - 'cantidad' (int): La cantidad del producto en el carrito.
     *      - 'precioTotal' (float): El precio total de esta línea del carrito.
     */

    public function __construct($attributes = []) {
        $this->producto = $attributes['producto'] ?? null;
        $this->cantidad = $attributes['cantidad'] ?? 1;
        $this->precioTotal = $attributes['precioTotal'] ?? 0;
    }

    /**
     * Método mágico __toString().
     *
     * Este método convierte la instancia de LineaCarrito en una representación de cadena.
     * Esto es útil para depuración o para imprimir la información de la línea del carrito.
     *
     * @return string Una representación de la línea de carrito, incluyendo el nombre del producto,
     *                la cantidad y el precio total.
     */
    public function __toString()
    {
        return "Producto: {$this->producto->nombre}, Cantidad: {$this->cantidad}, Total: {$this->precioTotal}";
    }

}
