<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\GuidGenerator;

/**
 * Clase Producto que representa un producto en el sistema de venta.
 *
 * Un producto tiene atributos como nombre, descripción, precio, stock y más.
 * Además, tiene relaciones con otros modelos como Cliente (vendedor) y Cliente (favoritos).
 */
class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['guid', 'vendedor_id', 'nombre', 'descripcion', 'estadoFisico', 'precio','stock', 'categoria', 'estado', 'imagenes'];

    protected $casts = [
        'imagenes' => 'array',
    ];

    /**
     * Boot del modelo: Genera un GUID al crear un nuevo producto si no existe uno.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($producto) {
            if (empty($producto->guid)) {
                $producto->guid = GuidGenerator::generarId();
            }
        });
    }

    /**
     * Relación de muchos a uno con el modelo Cliente (Vendedor).
     *
     * Un producto tiene un vendedor que es un cliente. Esta relación
     * permite acceder al vendedor que ha puesto a la venta el producto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendedor()
    {
        return $this->belongsTo(Cliente::class, 'vendedor_id');
    }

    /**
     * Relación de muchos a muchos con el modelo Cliente (Favoritos).
     *
     * Un producto puede ser marcado como favorito por varios clientes.
     * Esta relación permite acceder a los clientes que han marcado el producto como favorito.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function clientesFavoritos()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_favoritos', 'producto_id', 'cliente_id')->withTimestamps();
    }
}
