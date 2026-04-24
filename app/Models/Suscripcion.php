<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    use HasFactory;

    protected $table = 'suscripciones';

    protected $fillable = [
        'user_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'paypal_subscription_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
