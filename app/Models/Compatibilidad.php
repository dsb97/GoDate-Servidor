<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compatibilidad extends Model
{
    use HasFactory;

    protected $table = 'compatibilidad';
    protected $fillable = ['id_usuario_o', 'id_usuario_d', 'porcentaje'];

    public function usuarioCompatible() {
        return $this->hasOne(Usuario::class, 'id', 'id_usuario_d');
    }
}
