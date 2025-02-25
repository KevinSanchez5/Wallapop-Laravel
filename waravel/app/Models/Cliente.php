<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['guid', 'nombre', 'apellido', 'avatar', 'telefono', 'direccion', 'activo', 'usuario_guid'];

    // Relación 1-1 inversa con User
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_guid');
    }

    // Relación 1:N con Producto
    public function productos()
    {
        return $this->hasMany(Producto::class, 'vendedor_guid');
    }

    // Relación N:M -> Un cliente puede tener varios productos en favoritos
    public function favoritos()
    {
        return $this->belongsToMany(Producto::class, 'cliente_favoritos', 'cliente_guid', 'producto_guid')->withTimestamps();
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
