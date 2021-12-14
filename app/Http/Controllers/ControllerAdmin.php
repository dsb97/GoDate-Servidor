<?php

namespace App\Http\Controllers;

use App\Models\PrefUsuarios;
use App\Models\Usuario;
use Illuminate\Http\Request;
class ControllerAdmin extends Controller
{
    public function listarUsuarios() {
        $lista = Usuario::all();
        return request()->json($lista, 200);
    }
    public function altaUsuario(Request $r) {
        $usuario = new Usuario();
        $usuario->id='';
        $usuario->nombre='';
        $usuario->apellidos='';
        $usuario->correo='';
        $usuario->id_genero='';
        $usuario->fecha_nacimiento='';
        $usuario->ciudad='';
        $usuario->descripcion='';
        $usuario->foto='';
        $usuario->hijos='';
        $usuario->conectado='';
        $usuario->activo='';
        $usuario->tema='';
        $usuario->save();
        $preferencia = new PrefUsuarios();
        $preferencia->id_usuario='';
        $preferencia->id_preferencia='';
        $preferencia->intensidad='';
        $preferencia->save();
    }
    public function borrarUsuario (Request $r) {
        $usuario = Usuario::find($r->get('id'));
        if (!$usuario) {
            return response()->json(['error' => 'No se encuentra el usuario'], 404);
        }
        $usuario->delete();
        return response()->json(['mensaje' => 'OK'], 204);
    }

    public function togleActivado (Request $r){
        $usuario = Usuario::find($r);
        if(!$usuario) {
            return response()->json(['error' => 'No se encuentra el usuario'], 404);
        }
        $usuario->activo = ($usuario->activo == 1 ? 0 : 1);
        $usuario->save();
        return response()->json(['mensaje' => 'OK'], 204);
    }
}
