<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'plan',
        'rol'
    ];

    protected $attributes = [
        'plan' => 'free',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    // Relación con los apuntes (Un usuario puede tener muchos apuntes)
    public function apuntes()
    {
        return $this->hasMany(Apunte::class);
    }

    // Relación con las suscripciones (Un usuario puede tener solo una suscripción)
    public function suscripcion()
    {
        return $this->hasOne(Suscripcion::class);
    }

    // Relación con las descargas (Un usuario puede hacer muchas descargas)
    public function descargas()
    {
        return $this->hasMany(Descarga::class);
    }

    // Método para verificar si el usuario es premium
    public function esPremium()
    {
        return $this->plan === 'premium';
    }

}
