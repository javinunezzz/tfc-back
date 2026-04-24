<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Descarga extends Model
{
    use HasFactory;

    protected $table = 'descargas';

    protected $fillable = [
        'user_id',
        'apunte_id',
        'fecha_descarga'
    ];

    protected $dates = [
        'fecha_descarga'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apunte()
    {
        return $this->belongsTo(Apunte::class);
    }
}
