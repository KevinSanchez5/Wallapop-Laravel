<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Utils\GuidGenerator;
use App\Models\Cliente;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


/**
 * Modelo de Usuario que representa a los usuarios en el sistema.
 *
 * Un usuario puede tener un cliente asociado, y su información se utiliza para autenticación en el sistema.
 * Además, tiene una relación de uno a uno con el modelo `Cliente`, que es un perfil del usuario con más detalles.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'guid',
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'password_reset_token',
        'password_reset_expires_at',
    ];

    /**
     * El "boot" del modelo. Se ejecuta al crear un nuevo usuario.
     * Si el usuario no tiene un `guid`, se generará uno automáticamente.
     */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->guid)) {
                $user->guid = GuidGenerator::generarId();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación uno a uno con el modelo `Cliente`.
     *
     * Un usuario tiene un cliente asociado, lo cual nos permite acceder a la información adicional
     * relacionada con ese cliente, como el perfil, la dirección, etc.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'usuario_id');
    }

    /**
     * Scope para buscar un usuario por su correo electrónico.
     *
     * Este método se usa para obtener un usuario filtrando por su correo electrónico.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }
}
