<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    use HasFactory;

    protected $table = 'favoritos';

    protected $fillable = ['user_id', 'apunte_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apunte()
    {
        return $this->belongsTo(Apunte::class);
    }
}