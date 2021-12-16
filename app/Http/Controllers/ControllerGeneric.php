<?php

namespace App\Http\Controllers;

use App\Models\Preferencia;
use App\Models\Ciudad;
use App\Models\Usuario;
use App\Models\Pass;
use Illuminate\Http\Request;

class ControllerGeneric extends Controller
{
    public function login(Request $r) {
        $correo = $r->get('correo');
        $pass = $r->get('pass');

        $u = Usuario::where('correo', $correo)->get()[0];
        $p = Pass::where('id_usuario', $u->id)->get()[0];
        if ($u) {
            if($p->pass == md5($pass)){
                $rr = Usuario::where('correo', $correo)->get(['id', 'correo', 'nombre', 'apellidos', 'foto'])[0];
                return response()->json($rr, 200);
            } else {
                return response()->json(['mensaje' => 'Contraseña incorrecta'], 403);
            }
        } else {
            return response()->json(['mensaje' => 'Este usuario no está registrado'], 404);
        }


    }

    public function obtenerCiudadesFormulario () {
        return response()->json(Ciudad::get('ciudad'), 200);
    }

    public function obtenerPreferenciasFormulario () {
        return response()->json(Preferencia::get(['id', 'descripcion']), 200);
    }
}
