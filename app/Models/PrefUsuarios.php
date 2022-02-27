<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrefUsuarios extends Model
{
    use HasFactory;

    protected $table = 'preferencias_usuarios';
    protected $fillable = [
        'id_usuario',
        'id_preferencia',
        'intensidad'
    ];
}
