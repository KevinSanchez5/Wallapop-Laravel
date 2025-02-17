<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $fillable = ['guid', 'cliente', 'lineasCarrito', 'precioTotal'];

    public function setClienteAttribute($value)
    {
        $this->attributes['cliente'] = json_encode($value);
    }

    public function getClienteAttribute($value)
    {
        return json_decode($value);
    }

    public function setLineaCarritoAttribute($value)
    {
        $this->attributes['lineasCarrito'] = json_encode($value);
    }

    public function getLineaCarritoAttribute($value)
    {
        return json_decode($value);
    }
}
