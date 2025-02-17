<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaVenta extends Model
{
    protected $fillable = ['guid', 'vendedor', 'cantidad', 'producto', 'precio', 'precioTotal'];

    public function setVendedorAttribute($value)
    {
        $this->attributes['vendedor'] = json_encode($value);
    }

    public function getVendedorAttribute($value)
    {
        return json_decode($value);
    }

    public function setProductoAttribute($value)
    {
        $this->attributes['producto'] = json_encode($value);
    }

    public function getProductoAttribute($value)
    {
        return json_decode($value);
    }
}
