<?php

namespace App\Models;

use App\Utils\GuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clase Valoracion que representa una valoración hecha por un cliente a otro cliente.
 *
 * Una valoración contiene un comentario, una puntuación, un cliente valorado (quien recibe la valoración),
 * el autor de la valoración (quien la crea) y la venta relacionada a dicha valoración.
 */
class Valoracion extends Model
{
    use HasFactory;

    protected $table = 'valoraciones';

    protected $fillable = ['guid', 'comentario', 'puntuacion', 'clienteValorado_id', 'autor_id', 'venta_id'];

    /**
     * Boot del modelo: Genera un GUID al crear una nueva valoración si no existe uno.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($valoracion) {
            if (empty($valoracion->guid)) {
                $valoracion->guid = GuidGenerator::generarId();
            }
        });
    }

    /**
     * Relación con el cliente valorado (cliente que recibe la valoración).
     *
     * Esta relación indica que cada valoración pertenece a un cliente que es valorado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function clienteValorado()
    {
        return $this->belongsTo(Cliente::class, 'clienteValorado_id');
    }

    /**
     * Relación con el creador de la valoración (cliente que crea la valoración).
     *
     * Esta relación indica que cada valoración es creada por un cliente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creador()
    {
        return $this->belongsTo(Cliente::class, 'autor_id');
    }

    /**
     * Relación con la venta a la que pertenece la valoración.
     *
     * Esta relación indica que cada valoración está asociada a una venta específica.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
