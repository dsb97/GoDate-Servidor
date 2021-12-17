<?php

namespace App\Http\Controllers;

use App\Models\PrefUsuarios;
use App\Models\Usuario;
use Illuminate\Http\Request;
class ControllerAdmin extends Controller
{
    /**
     * Lista de usuarios con sus propiedades
     */
    public function listarUsuarios() {
        $lista = Usuario::all();
        return request()->json($lista, 200);
    }
    /**
     * Función que regisrea un usuario en la base de datos
     * según la información pasada a través de la petición
     * @param Request $r
     *
     */
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

    /**
     * Borra un usuario de la BBDD
     * @param Request $r
     *
     */
    public function borrarUsuario (Request $r) {
        $usuario = Usuario::find($r->get('id'));
        if (!$usuario) {
            return response()->json(['error' => 'No se encuentra el usuario'], 404);
        }
        $usuario->delete();
        return response()->json(['mensaje' => 'OK'], 200);
    }

    /**
     * Activa o desactiva al usuario enviado por la petición
     * @param Request $r
     */
    public function togleActivado (Request $r){
        $usuario = Usuario::find($r);
        if(!$usuario) {
            return response()->json(['error' => 'No se encuentra el usuario'], 404);
        }
        $usuario->activo = ($usuario->activo == 1 ? 0 : 1);
        $usuario->save();
        return response()->json(['mensaje' => 'OK'], 200);
    }
}
