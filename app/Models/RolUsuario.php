<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolUsuario extends Model
{
    use HasFactory;
    protected $table = 'roles_usuario';
    protected $fillable = [
        'id_usuario',
        'id_rol'
    ];
    public $timestamps = false;

}
