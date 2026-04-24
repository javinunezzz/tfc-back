<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apunte extends Model
{
    use HasFactory;

    protected $table = 'apuntes';

    protected $fillable = [
        'user_id',
        'categoria_id',
        'asignatura_id',
        'titulo',
        'descripcion',
        'pdf'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class);
    }
}
