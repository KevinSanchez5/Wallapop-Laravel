<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['guid', 'nombre', 'apellido', 'avatar', 'telefono', 'direccion', 'activo', 'usuario_id', 'favoritos'];

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
        return $this->hasMany(Valoracion::class, 'clienteValorado_id');
    }

    // Relación con las valoraciones que ha creado un cliente.
    public function valoracionesCreadas()
    {
        return $this->hasMany(Valoracion::class, 'creador_id');
    }

    public function setDireccionAttribute($value)
    {
        $this->attributes['direccion'] = json_encode($value);
    }

    public function getDireccionAttribute($value)
    {
        return json_decode($value);
    }
}
