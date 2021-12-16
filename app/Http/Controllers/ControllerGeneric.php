<?php

namespace App\Http\Controllers;

use App\Models\Preferencia;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ControllerGeneric extends Controller
{
    public function login(Request $r) {
        $correo = $r->get('correo');
        $pass = $r->get('pass');

        $u = Usuario::where('correo', $correo)->get();
        if (count($u)) {
            if($u->pass == md5($pass)){
                return request()->json(Usuario::where('correo', $correo)->get(['id', 'nombre', 'apellidos', 'foto']), 200);
            } else {
                return request()->json(['mensaje' => 'Contraseña incorrecta'], 403);
            }
        } else {
            return request()->json(['mensaje' => 'Este usuario no está registrado'], 404);
        }
    }

    public function obtenerCiudadesFormulario () {

    }

    public function obtenerPreferenciasFormulario () {
        return response()->json(Preferencia::get(['id', 'descripcion']), 200);
    }
}
