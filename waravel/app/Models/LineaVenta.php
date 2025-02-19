<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaVenta extends Model
{
    protected $fillable = ['guid', 'vendedor', 'cantidad', 'producto', 'precioTotal'];

    public function setVendedorAttribute($value)
    {
        $this->attributes['vendedor'] = json_encode($value);
    }

    public function getVendedorAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value);
    }

    public function setProductoAttribute($value)
    {
        $this->attributes['producto'] = json_encode($value);
    }

    public function getProductoAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value);
    }
}
