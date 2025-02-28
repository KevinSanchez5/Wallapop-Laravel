<?php

namespace App\Models;

use App\Utils\GuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{

    use HasFactory;
    protected $fillable = ['guid', 'comprador', 'lineaVentas', 'precioTotal', 'estado'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venta) {
            $venta->guid = GuidGenerator::generarId();
        });
    }

    public function setCompradorAttribute($value)
    {
        $this->attributes['comprador'] = json_encode($value);
    }

    public function getCompradorAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value);
    }

    public function setLineaVentasAttribute($value)
    {
        $this->attributes['lineaVentas'] = json_encode($value);
    }

    public function getLineaVentasAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }
}
