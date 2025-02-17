<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = ['guid', 'comprador', 'lineaVentas', 'precioTotal'];

    public function setCompradorAttribute($value)
    {
        $this->attributes['comprador'] = json_encode($value);
    }

    public function getCompradorAttribute($value)
    {
        return json_decode($value);
    }

    public function setLineaVentaAttribute($value)
    {
        $this->attributes['lineaVentas'] = json_encode($value);
    }

    public function getLineaVentaAttribute($value)
    {
        return json_decode($value);
    }
}
