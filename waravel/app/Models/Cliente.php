<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\GuidGenerator;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['guid', 'nombre', 'apellido', 'avatar', 'telefono', 'direccion', 'activo', 'usuario_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cliente) {
            $cliente->guid = GuidGenerator::generarId();
        });
    }

    // Relación 1-1 inversa con User
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relación 1:N con Producto
    public function productos()
    {
        return $this->hasMany(Producto::class, 'vendedor_id');
    }

    // Relación N:M -> Un cliente puede tener varios productos en favoritos
    public function favoritos()
    {
        return $this->belongsToMany(Producto::class, 'cliente_favoritos', 'cliente_id', 'producto_id')->withTimestamps();
    }

    // Relación con las valoraciones que ha recibido un cliente.
    public function valoracionesRecibidas()
    {
        return $this->hasMany(Valoracion::class, 'clienteValorado_guid');
    }

    // Relación con las valoraciones que ha creado un cliente.
    public function valoracionesCreadas()
    {
        return $this->hasMany(Valoracion::class, 'creador_guid');
    }

    public function setDireccionAttribute($value)
    {
        $this->attributes['direccion'] = json_encode($value);
    }

    public function getDireccionAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value);
    }

}
