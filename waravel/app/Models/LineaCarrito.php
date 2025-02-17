<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaCarrito extends Model
{
    protected $fillable = ['guid', 'producto', 'cantidad', 'precioTotal'];

    public function setProductoAttribute($value)
    {
        $this->attributes['producto'] = json_encode($value);
    }

    public function getProductoAttribute($value)
    {
        return json_decode($value);
    }
}
