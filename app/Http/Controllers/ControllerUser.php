<?php

namespace App\Http\Controllers;

use App\Models\Compatibilidad;
use App\Models\Dislike;
use App\Models\Genero;
use App\Models\Like;
use App\Models\Preferencia;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ControllerUser extends Controller
{

    /**
     * Devuelve una lista de gente afín al usuario introducido
     * @param integer $idUsuario
     */
    public function listarAfinidades($idUsuario)
    {
        $uC = Usuario::with(['likesDados', 'dislikesDados', 'compatibilidadOrigen', 'compatibilidadDestino', 'preferencias', 'gustosGenero'])->find($idUsuario);
        $idPreferenciaEdadMax = Preferencia::where('descripcion', 'like', '%dad%n%')->get('id')->pluck('id')->toArray()[0];
        $idPreferenciaEdadMin = Preferencia::where('descripcion', 'like', '%dad%x%')->get('id')->pluck('id')->toArray()[0];

        //Añadido filtro por edad
        $edadMin = $this->getAnio($uC->preferencias->where('id_preferencia', $idPreferenciaEdadMin)->first()->intensidad);
        $edadMax = $this->getAnio($uC->preferencias->where('id_preferencia', $idPreferenciaEdadMax)->first()->intensidad);

        //dd($edadMax . ',' . $edadMin);
        $lista = Usuario::with('preferencias')
            ->where([
                ['id', '<>', $idUsuario],
                ['fecha_nacimiento', '>', $edadMin],
                ['fecha_nacimiento', '<', $edadMax]
            ])
            ->whereIn('id_genero', $uC->gustosGenero->pluck('id_genero')->toArray())
            ->whereNotIn('id', $uC->likesDados->pluck('id_usuario_d')->toArray())
            ->whereNotIn('id', $uC->dislikesDados->pluck('id_usuario_d')->toArray())
            ->orderBy('id', 'asc')
            ->take(100)
            ->get();





        if (count($uC->likesDados) + count($uC->dislikesDados) == count($uC->compatibilidadOrigen)
            || (count($uC->compatibilidadOrigen) != count($lista))) {
            $this->generarCompatibilidad($lista, Usuario::with('preferencias')->find($idUsuario));
        }


        $idsCompatibilidades = Usuario::with('compatibilidadOrigen')->find($idUsuario)->compatibilidadOrigen->pluck('id_usuario_d')->toArray();

        $listaDevolver = Usuario::join('compatibilidad', 'usuarios.id', '=', 'compatibilidad.id_usuario_d')
            ->join('generos', 'generos.id', '=', 'usuarios.id_genero')
            ->where('compatibilidad.id_usuario_o', $idUsuario)
            ->whereIn('usuarios.id', $idsCompatibilidades)
            ->whereNotIn('usuarios.id', $uC->likesDados->pluck('id_usuario_d')->toArray())
            ->whereNotIn('usuarios.id', $uC->dislikesDados->pluck('id_usuario_d')->toArray())
            ->orderBy('compatibilidad.porcentaje', 'desc')
            ->select(['usuarios.id', 'usuarios.nombre', 'usuarios.apellidos', 'generos.descripcion as genero', 'usuarios.fecha_nacimiento', 'usuarios.ciudad', 'usuarios.descripcion', 'usuarios.foto', 'usuarios.hijos', 'usuarios.conectado', 'usuarios.activo', 'compatibilidad.porcentaje'])
            ->take(100)
            ->get();
        return response()->json($listaDevolver, 200);
    }

    /**
     * Obtiene una fecha formateada según la edad introducida
     * para compararla con las edades mínimas y máximas que el usuario
     * indique en sus preferencias
     */
    private function getAnio($y) {
        return date("Y") - intval($y.'') . '-01-01';
    }
    /**
     * Genera un porcentaje de compatibilidad para cada uno de los usuarios de la lista dada
     * @param mixed $lista
     * @param mixed $yo
     * @return void
     */
    private function generarCompatibilidad($lista, $yo)
    {
        foreach ($lista as $usuario) {
            $suma = 0;
            $countPrefVal = 0;
            foreach ($usuario->preferencias as $preferencia) {
                if (!str_contains(strtolower(Preferencia::where('id', 1)->get()->first()->descripcion), 'edad')
                || !str_contains(strtolower(Preferencia::where('id', 1)->get()->first()->descripcion), 'relac')) {
                    $maximo = max($preferencia->intensidad, $yo->preferencias->where('id_preferencia', $preferencia->id_preferencia)->first()->intensidad);
                    $minimo = min($preferencia->intensidad, $yo->preferencias->where('id_preferencia', $preferencia->id_preferencia)->first()->intensidad);
                    $suma += $this->calcularPorcentajeCompatibilidad($maximo, $minimo);
                    $countPrefVal++;
                }
            }

            $porcentajeTotal = $countPrefVal * 100;
            $compatibilidad = $this->calcularPorcentajeCompatibilidad($porcentajeTotal, $suma);

            //Insertamos en la tabla en ambos sentidos:
            if (count(Compatibilidad::where([['id_usuario_o', $yo->id], ['id_usuario_d', $usuario->id]])->get()) == 1) {
                Compatibilidad::where([['id_usuario_o', $yo->id], ['id_usuario_d', $usuario->id]])->update(['porcentaje' => $compatibilidad]);
            } else {
                Compatibilidad::create([
                    'id_usuario_o' => $yo->id,
                    'id_usuario_d' => $usuario->id,
                    'porcentaje' => $compatibilidad
                ]);
            }

            if (count(Compatibilidad::where([['id_usuario_o', $usuario->id], ['id_usuario_d', $yo->id]])->get()) == 1) {
                Compatibilidad::where([['id_usuario_o', $usuario->id], ['id_usuario_d', $yo->id]])->update(['porcentaje' => $compatibilidad]);
            } else {
                Compatibilidad::create([
                    'id_usuario_o' => $usuario->id,
                    'id_usuario_d' => $yo->id,
                    'porcentaje' => $compatibilidad
                ]);
            }
        }
    }

    /**
     * Calcula un porcentaje entre dos valores
     * @param integer $maximo Valor del límite superior del cálculo
     * @param integer $minimo Valor del límite inferior del cálculo
     */
    private function calcularPorcentajeCompatibilidad($maximo, $minimo)
    {
        $devolver = 0;
        try {
            $devolver = (($minimo * 100) / $maximo);
        } catch (\Throwable $th) {
            $devolver = 0;
        }
        return $devolver;
    }

    /**
     * Devuelve la información del perfil del usuario
     * @param integer $idUsuario
     */
    public function perfilUsuario($idUsuario)
    {
        return response()->json(Usuario::with(['preferencias','pass'])->find($idUsuario), 200);
    }

    /**
     * Inserta un like entre dos usuarios
     * @param Request $r
     */
    public function like(Request $r)
    {
        $id_usuario_origen = $r->id_o;
        $id_usuario_destino = $r->id_d;


        $l = Like::where([
            ['id_usuario_o', $id_usuario_origen],
            ['id_usuario_d', $id_usuario_destino]
        ])->get();

        if (count($l) == 0) {
            Like::create([
                'id_usuario_o' => $id_usuario_origen,
                'id_usuario_d' => $id_usuario_destino
            ]);
        }

        return response()->json($this->esMatch($id_usuario_origen, $id_usuario_destino) ? 'true' : 'false', 200);
    }

    /**
     * Devuelve true si dos usuarios han obtenido un like mutuo
     * @param integer $id_usuario_origen
     * @param integer $id_usuario_destino
     * @return boolean
     */
    private function esMatch($id_usuario_origen, $id_usuario_destino)
    {
        return count(Like::where([
            ['id_usuario_o', $id_usuario_destino],
            ['id_usuario_d', $id_usuario_origen]
        ])->get()) > 0;
    }

    /**
     * Inserta un dislike entre dos usuarios
     */
    public function dislike(Request $r)
    {
        $id_usuario_origen = $r->id_o;
        $id_usuario_destino = $r->id_d;


        $dl = Dislike::where([
            ['id_usuario_o', $id_usuario_origen],
            ['id_usuario_d', $id_usuario_destino]
        ])->get();

        if (count($dl) == 0) {
            Dislike::create([
                'id_usuario_o' => $id_usuario_origen,
                'id_usuario_d' => $id_usuario_destino
            ]);
        }

        return response()->json(['mensaje' => 'ok'], 200);
    }

    public function borrarPerfil($idUsuario)
    {
    }

    public function cerrarSesion ($idUsuario) {
        $u = Usuario::find($idUsuario);
        $u->conectado = 0;
        $u->save();
        return response()->json(200);
    }

    /**
     * Lista las personas con un like mutuo
     */
    public function listarAmigos($idUsuario)
    {
        $idsLikesRecibidos = Usuario::with('likesRecibidos')
            ->where('id', $idUsuario)
            ->get()
            ->first()
            ->likesRecibidos
            ->pluck('id_usuario_o')
            ->toArray();

        $idsLikesDados = Usuario::with('likesDados')
            ->where('id', $idUsuario)
            ->get()
            ->first()
            ->likesDados
            ->pluck('id_usuario_d')
            ->toArray();

        $idsCoincidentes = array_intersect($idsLikesRecibidos, $idsLikesDados);

        $listaUsuarios = Usuario::whereIn('id', $idsCoincidentes)
            ->select('id', 'nombre', 'apellidos', 'fecha_nacimiento', 'foto', 'conectado')
            ->get();
        return response()->json($listaUsuarios, 200);
    }

    /**
     * Lista las personas de la misma ciudad que el usuario
     */
    public function listarGenteCerca($idUsuario)
    {
        $ciudadUsuario = Usuario::where('id', $idUsuario)->get()->first()->ciudad;
        $listaGenteCerca = Usuario::where([
            ['ciudad', $ciudadUsuario],
            ['id', '<>', $idUsuario],
        ])
            ->select('id', 'nombre', 'apellidos', 'fecha_nacimiento', 'foto', 'conectado')
            ->get();
        return response()->json($listaGenteCerca, 200);
    }

    /**
     * Lista las personas que han dado like al perfil del usuario
     */
    public function listarLesGusto($idUsuario)
    {
        $idsLikesRecibidos = Usuario::with('likesRecibidos')
            ->where('id', $idUsuario)
            ->get()
            ->first()
            ->likesRecibidos
            ->pluck('id_usuario_o')
            ->toArray();

        $idsLikesDados = Usuario::with('likesDados')
            ->where('id', $idUsuario)
            ->get()
            ->first()
            ->likesDados
            ->pluck('id_usuario_d')
            ->toArray();

        $idsCoincidentes = array_diff($idsLikesRecibidos, $idsLikesDados);

        $listaUsuarios = Usuario::whereIn('id', $idsCoincidentes)
            ->select('id', 'nombre', 'apellidos', 'fecha_nacimiento', 'foto', 'conectado')
            ->get();
        return response()->json($listaUsuarios, 200);
    }
}
