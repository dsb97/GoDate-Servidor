<?php

namespace App\Http\Controllers;

use App\Models\Compatibilidad;
use App\Models\Genero;
use App\Models\Like;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ControllerUser extends Controller
{
    public function listarAfinidades($idUsuario) {
        $uC = Usuario::with(['likesDados', 'dislikesDados','compatibilidadOrigen', 'compatibilidadDestino', 'preferencias', 'gustosGenero'])->find($idUsuario);

        //Falta filtrar por edad :>
        $lista = Usuario::with('preferencias')
                    ->where('id', '<>', $idUsuario)
                    ->whereIn('id_genero', $uC->gustosGenero->pluck('id_genero')->toArray())
                    ->whereNotIn('id', $uC->likesDados->pluck('id_usuario_d')->toArray())
                    ->whereNotIn('id', $uC->dislikesDados->pluck('id_usuario_d')->toArray())
                    ->orderBy('id', 'asc')
                    ->take(100)
                    ->get();

        if(count($uC->likesDados) + count($uC->dislikesDados) == count($uC->compatibilidadOrigen)
        || (count($uC->compatibilidadOrigen) != count($lista))) {
            $this->generarCompatibilidad($lista, Usuario::with('preferencias')->find($idUsuario));
        }


        $idsCompatibilidades = Usuario::with('compatibilidadOrigen')->find($idUsuario)->compatibilidadOrigen->pluck('id_usuario_d')->toArray();

        $listaDevolver = Usuario::whereIn('id', $idsCompatibilidades)
        ->whereNotIn('id', $uC->likesDados->pluck('id_usuario_d')->toArray())
        ->whereNotIn('id', $uC->dislikesDados->pluck('id_usuario_d')->toArray())
        ->get();
        return response()->json($listaDevolver, 200);
    }


    private function generarCompatibilidad($lista, $yo) {
        foreach ($lista as $usuario) {
            $s = 0;
            foreach ($usuario->preferencias as $preferencia) {
                $mx = max($preferencia->intensidad, $yo->preferencias->where('id_preferencia', $preferencia->id_preferencia)->first()->intensidad);
                $mn = min($preferencia->intensidad, $yo->preferencias->where('id_preferencia', $preferencia->id_preferencia)->first()->intensidad);
                $s += $this->calcularPorcentajeCompatibilidad($mx, $mn);
            }

            $p = count($usuario->preferencias) * 100;
            $comp = $this->calcularPorcentajeCompatibilidad($p, $s);

            //Insertamos en la tabla en ambos sentidos:
            if(count(Compatibilidad::where([['id_usuario_o', $yo->id],['id_usuario_d',$usuario->id]])->get()) == 1){
                Compatibilidad::where([['id_usuario_o', $yo->id],['id_usuario_d',$usuario->id]])->update(['porcentaje'=>$comp]);
            } else {
                Compatibilidad::create([
                    'id_usuario_o' => $yo->id,
                    'id_usuario_d' => $usuario->id,
                    'porcentaje' => $comp
                ]);
            }

            if(count(Compatibilidad::where([['id_usuario_o', $usuario->id],['id_usuario_d',$yo->id]])->get()) == 1){
                Compatibilidad::where([['id_usuario_o', $usuario->id],['id_usuario_d',$yo->id]])->update(['porcentaje'=>$comp]);
            } else {
                Compatibilidad::create([
                    'id_usuario_o' => $usuario->id,
                    'id_usuario_d' => $yo->id,
                    'porcentaje' => $comp
                ]);
            }


        }
    }

    private function calcularPorcentajeCompatibilidad($mx, $mn) {
        $ret = 0;
        try {
            $ret = (($mn * 100) / $mx);
        } catch (\Throwable $th) {
            $ret = 0;
        }
        return $ret;
    }

    public function perfilUsuario($idUsuario) {
        return response(Usuario::with('preferencias')->find($idUsuario), 200);

    }

    public function like($id_u_o, $id_u_d) {
        Like::create([
            'id_usuario_o' => $id_u_o,
            'id_usuario_d' => $id_u_d
        ]);

    }

    public function dislike() {

    }

    public function borrarPerfil() {

    }
}
