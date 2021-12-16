<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'apellidos', 'correo', 'pass', 'id_genero', 'fecha_nacimiento', 'ciudad', 'descripcion', 'foto', 'conectado', 'activo', 'tema'];
    public function pass()
    {
        return $this->hasOne(Pass::class, 'id_usuario', 'id');
    }
    public function gustosGenero()
    {
        return $this->hasMany(GustosGenero::class, 'id_usuario', 'id');
    }
    public function preferencias()
    {
        return $this->hasMany(PrefUsuarios::class, 'id_usuario', 'id');
    }
    public function likesDados()
    {
        return $this->hasMany(Like::class, 'id_usuario_o', 'id');
    }
    public function likesRecibidos()
    {
        return $this->hasMany(Like::class, 'id_usuario_d', 'id');
    }
    public function dislikesDados()
    {
        return $this->hasMany(Dislike::class, 'id_usuario_o', 'id');
    }
    public function dislikesRecibidos()
    {
        return $this->hasMany(Dislike::class, 'id_usuario_d', 'id');
    }
    public function compatibilidadOrigen()
    {
        return $this->hasMany(Compatibilidad::class, 'id_usuario_o', 'id');
    }
    public function compatibilidadDestino()
    {
        return $this->hasMany(Compatibilidad::class, 'id_usuario_d', 'id');
    }
}
