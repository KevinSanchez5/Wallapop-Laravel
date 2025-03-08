<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase LineaVenta que representa una línea de venta en un pedido.
 *
 * Cada línea de venta contiene información sobre un producto, su cantidad y el precio total
 * de esa línea de venta. Además, almacena detalles sobre el vendedor que ha realizado la venta.
 */
class LineaVenta extends Model
{
    protected $fillable = ['guid', 'vendedor', 'cantidad', 'producto', 'precioTotal'];

    /**
     * Establece el atributo 'vendedor' como un valor JSON.
     *
     * Al guardar un vendedor en la base de datos, se convierte a formato JSON.
     *
     * @param mixed $value El valor que se va a asignar al atributo 'vendedor'.
     */
    public function setVendedorAttribute($value)
    {
        $this->attributes['vendedor'] = json_encode($value);
    }

    /**
     * Obtiene el atributo 'vendedor' y lo convierte desde JSON a un array.
     *
     * Este método se ejecuta cuando el atributo 'vendedor' se accede desde la base de datos,
     * convirtiendo su valor JSON a un array en lugar de una cadena JSON.
     *
     * @param string $value El valor almacenado en la base de datos en formato JSON.
     * @return array El valor del atributo 'vendedor' convertido en un array.
     */
    public function getVendedorAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value);
    }

    /**
     * Establece el atributo 'producto' como un valor JSON.
     *
     * Al guardar un producto en la base de datos, se convierte a formato JSON.
     *
     * @param mixed $value El valor que se va a asignar al atributo 'producto'.
     */
    public function setProductoAttribute($value)
    {
        $this->attributes['producto'] = json_encode($value);
    }

    /**
     * Obtiene el atributo 'producto' y lo convierte desde JSON a un array.
     *
     * Este método se ejecuta cuando el atributo 'producto' se accede desde la base de datos,
     * convirtiendo su valor JSON a un array en lugar de una cadena JSON.
     *
     * @param string $value El valor almacenado en la base de datos en formato JSON.
     * @return array El valor del atributo 'producto' convertido en un array.
     */

    public function getProductoAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value);
    }
}
