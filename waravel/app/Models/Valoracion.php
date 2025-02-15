<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    use HasFactory;

    protected $fillable = ['guid', 'comentario', 'puntuacion', 'clienteValorado_id', 'autor_id'];

    /**
     * Relación con el cliente valorado (cliente que recibe la valoración).
     */
    public function clienteValorado()
    {
        return $this->belongsTo(Cliente::class, 'clienteValorado_id');
    }

    /**
     * Relación con el creador de la valoración (cliente que crea la valoración).
     */
    public function creador()
    {
        return $this->belongsTo(Cliente::class, 'autor_id');
    }
}
