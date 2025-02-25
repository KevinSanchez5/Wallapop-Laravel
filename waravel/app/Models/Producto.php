<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['guid', 'vendedor_guid', 'nombre', 'descripcion', 'estadoFisico', 'precio','stock', 'categoria', 'estado', 'imagenes'];

    protected $casts = [
        'imagenes' => 'array',
    ];

    // Relación N:1 con Cliente (Vendedor)
    public function vendedor()
    {
        return $this->belongsTo(Cliente::class, 'vendedor_guid');
    }

    // Relación N:M -> Un producto puede ser favorito de varios clientes
    public function clientesFavoritos()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_favoritos', 'producto_guid', 'cliente_guid')->withTimestamps();
    }
}
