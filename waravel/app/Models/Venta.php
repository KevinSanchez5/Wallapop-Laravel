<?php

namespace App\Models;

use App\Utils\GuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clase Venta que representa una venta en el sistema.
 *
 * Una venta tiene atributos como el comprador, las líneas de venta, el precio total y el estado de la venta.
 * Además, maneja la creación de un GUID único al crear una nueva venta y las conversiones de los atributos
 * `comprador` y `lineaVentas` de JSON a array.
 */
class Venta extends Model
{

    use HasFactory;
    protected $fillable = ['guid', 'comprador', 'lineaVentas', 'precioTotal', 'estado', 'payment_intent_id'];

    /**
     * Boot del modelo: Genera un GUID al crear una nueva venta si no existe uno.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venta) {
            if (empty($venta->guid)) {
                $venta->guid = GuidGenerator::generarId();
            }
        });
    }

    /**
     * Establece el valor del atributo `comprador` en formato JSON.
     *
     * Convierte el valor de `$value` en una cadena JSON antes de almacenarlo en la base de datos.
     *
     * @param mixed $value
     * @return void
     */

    public function setCompradorAttribute($value)
    {
        $this->attributes['comprador'] = json_encode($value);
    }

    /**
     * Obtiene el valor del atributo `comprador` desde la base de datos.
     *
     * Convierte el valor del atributo almacenado en la base de datos (en formato JSON) en un array o valor
     * adecuado cuando se accede al atributo.
     *
     * @param string $value
     * @return mixed
     */

    public function getCompradorAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value);
    }

    /**
     * Establece el valor del atributo `lineaVentas` en formato JSON.
     *
     * Convierte el valor de `$value` en una cadena JSON antes de almacenarlo en la base de datos.
     *
     * @param mixed $value
     * @return void
     */

    public function setLineaVentasAttribute($value)
    {
        $this->attributes['lineaVentas'] = json_encode($value);
    }

    /**
     * Obtiene el valor del atributo `lineaVentas` desde la base de datos.
     *
     * Convierte el valor del atributo almacenado en la base de datos (en formato JSON) en un array o valor
     * adecuado cuando se accede al atributo.
     *
     * @param string $value
     * @return mixed
     */

    public function getLineaVentasAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }
}
