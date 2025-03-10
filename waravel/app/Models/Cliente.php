<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\GuidGenerator;

class Cliente extends Model
{
    use HasFactory;

    /**
     * @var array $fillable Atributos que se pueden asignar de forma masiva.
     * Estos son los campos que pueden ser completados al crear o actualizar un cliente.
     */

    protected $fillable = ['guid', 'nombre', 'apellido', 'avatar', 'telefono', 'direccion', 'activo', 'usuario_id'];

    /**
     * Método de inicialización del modelo.
     *
     * Este método es utilizado para realizar configuraciones adicionales antes de que se guarde el modelo.
     * En este caso, asigna un `guid` único al crear un cliente si no se proporciona uno.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cliente) {
            if (empty($cliente->guid)) {
                $cliente->guid = GuidGenerator::generarId();
            }
        });
    }

    /**
     * Relación de 1 a 1 inversa con el modelo User.
     *
     * Un cliente está asociado a un único usuario a través de la clave foránea 'usuario_id'.
     * Esto permite acceder al usuario asociado a un cliente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación de 1 a N con el modelo Producto.
     *
     * Un cliente puede tener varios productos en venta. Esta relación permite acceder a los productos
     * que un cliente ha creado, utilizando la clave foránea 'vendedor_id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'vendedor_id');
    }

    /**
     * Relación N a M con el modelo Producto (favoritos).
     *
     * Un cliente puede tener varios productos en su lista de favoritos y un producto puede ser
     * favorito de varios clientes. Esta relación se maneja mediante la tabla intermedia 'cliente_favoritos'.
     * Se incluye `withTimestamps()` para mantener la información sobre cuándo se añadió un favorito.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoritos()
    {
        return $this->belongsToMany(Producto::class, 'cliente_favoritos', 'cliente_id', 'producto_id')->withTimestamps();
    }

    /**
     * Relación 1 a N con el modelo Valoracion (valoraciones recibidas).
     *
     * Un cliente puede recibir varias valoraciones, y esta relación permite acceder a todas
     * las valoraciones que el cliente ha recibido.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function valoracionesRecibidas()
    {
        return $this->hasMany(Valoracion::class, 'clienteValorado_guid');
    }

    /**
     * Relación 1 a N con el modelo Valoracion (valoraciones creadas).
     *
     * Un cliente puede crear varias valoraciones para productos o para otros usuarios.
     * Esta relación permite acceder a todas las valoraciones que el cliente ha creado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function valoracionesCreadas()
    {
        return $this->hasMany(Valoracion::class, 'creador_guid');
    }

    /**
     * Establece el atributo 'direccion' como un objeto JSON al guardar el cliente.
     *
     * Este mutador convierte la dirección en formato JSON antes de guardarla en la base de datos.
     *
     * @param mixed $value El valor de la dirección a guardar.
     */

    public function setDireccionAttribute($value)
    {
        $this->attributes['direccion'] = json_encode($value);
    }

    /**
     * Obtiene el atributo 'direccion' como un array cuando se accede al cliente.
     *
     * Este accesor convierte la dirección almacenada en formato JSON en un array.
     *
     * @param mixed $value El valor de la dirección almacenada en la base de datos.
     * @return array La dirección decodificada como un array.
     */

    public function getDireccionAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value);
    }

    public static function getUserEmailByClientGuid($clientGuid)
    {
        $cliente = self::where('guid', $clientGuid)->first();

        if (!$cliente) {
            return null;
        }

        $user = $cliente->usuario;

        if (!$user) {
            return null;
        }

        return $user->email;
    }

}
