<?php

namespace App\Http\Controllers;

use App\Models\GustosGenero;
use App\Models\Pass;
use App\Models\PrefUsuarios;
use App\Models\Rol;
use App\Models\RolUsuario;
use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;

class ControllerAdmin extends Controller
{

    /**
     * Función que registra un usuario en la base de datos
     * según la información pasada a través de la petición
     * @param Request $r
     * @return Response
     */
    public function altaUsuario(Request $r)
    {
        $id = 0;

        try {
            //Creamos el usuario
            $usuario = Usuario::create([
                'nombre' => $r->get('nombre'),
                'apellidos' => $r->get('apellidos'),
                'correo' => $r->get('correo'),
                'id_genero' => $r->get('id_genero'),
                'fecha_nacimiento' => $r->get('fecha_nacimiento'),
                'ciudad' => $r->get('ciudad'),
                'descripcion' => $r->get('descripcion'),
                'foto' => $r->get('foto'),
                'hijos' => $r->get('hijos'),
                'conectado' => 0,
                'activo' => $r->get('activo'),
                'tema' => 0,
            ]);

            $id = $usuario->id;

            //Le asignamos la contraseña
            Pass::create([
                'id_usuario' => $id,
                'pass' => md5($r->get('pass'))
            ]);

            //Insertamos los gustos de género
            foreach ($r->gustosGenero as $val) {
                GustosGenero::create([
                    'id_usuario' => $id,
                    'id_genero' => $val
                ]);
            }

            //Insertamos las preferencias
            foreach ($r->preferencias as $val) {
                PrefUsuarios::create([
                    'id_usuario' => $id,
                    'id_preferencia' => $val['id'],
                    'intensidad' => $val['intensidad']
                ]);
            }

            //Insertamos los roles
            //Cambio 01-03-2022
            foreach ($r->roles as $rol) {
                RolUsuario::create([
                    'id_usuario' => $id,
                    'id_rol' => $rol
                ]);
            }


            return response()->json(['mensaje' => 'Usuario registrado correctamente'], 200);
        } catch (Exception $ex) {
            $u = Usuario::find($id);

            if ($u) {
                $u->delete();
            }

            return response()->json(['mensaje' => 'No se ha podido crear el usuario, error interno del servidor. Mensaje de error: ' . $ex->getMessage()], 500);
        }
    }


    /**
     * Lista de usuarios con sus propiedades
     * @return Response
     */
    public function listarUsuarios()
    {
        $lista = Usuario::all('id', 'nombre', 'apellidos', 'foto', 'conectado', 'activo');
        return response()->json($lista, 200);
    }

    /**
     * Devuelve un objeto con el detalle del usuario indicado por parámetro
     * @param String $id ID del usuario a recuperar
     * @return Response
     */
    public function detalleUsuario($id)
    {
        $usuario = Usuario::where('id', '=', $id)
            ->select(
                'id',
                'nombre',
                'apellidos',
                'correo',
                'id_genero',
                'fecha_nacimiento',
                'ciudad',
                'descripcion',
                'foto',
                'hijos',
                'activo'
            )
            ->get()->first();

        if (!$usuario) {
            return response()->json(['mensaje' => 'El usuario indicado no se ha encontrado'], 400);
        } else {
            $gustos_genero = GustosGenero::where('id_usuario', '=', $id)
                ->get('id_genero')->pluck('id_genero');

            $preferencias = PrefUsuarios::join('preferencias', 'preferencias_usuarios.id_preferencia', '=', 'preferencias.id')
                ->where('preferencias_usuarios.id_usuario', '=', $id)
                ->select(
                    'preferencias.id',
                    'preferencias.descripcion',
                    'preferencias_usuarios.intensidad'
                )
                ->get();

            $usuario->gustosGenero = $gustos_genero;
            $usuario->preferencias = $preferencias;
            //Cambio 01-03-2022: Incorporación de roles en la respuesta
            $roles = RolUsuario::where('id_usuario', '=', $usuario->id)
                ->get()->pluck('id_rol')->toArray();
            $usuario->roles = $roles;

            return response()->json($usuario, 200);
        }
    }


    /**
     * Actualiza el usuario indicado por parámetro
     * @param String $id ID del usuario a actualizar
     * @return Response
     */
    public function actualizarUsuario(Request $r)
    {
        error_log($r->get('conectado'));

        try {
            //Obtenemos y actualizamos el usuario
            Usuario::where('id', '=', $r->get('id'))
                ->update([
                    'nombre' => $r->get('nombre'),
                    'apellidos' => $r->get('apellidos'),
                    'correo' => $r->get('correo'),
                    'id_genero' => $r->get('id_genero'),
                    'fecha_nacimiento' => $r->get('fecha_nacimiento'),
                    'ciudad' => $r->get('ciudad'),
                    'descripcion' => $r->get('descripcion'),
                    'foto' => $r->get('foto'),
                    'hijos' => $r->get('hijos'),
                    'activo' => $r->get('activo'),
                    'tema' => 0
                ]);



            //Le asignamos la contraseña
            if ($r->get('pass') != '') {
                Pass::where('id_usuario', '=', $r->get('id'))
                ->update([
                    'id_usuario' => $r->get('id'),
                    'pass' => md5($r->get('pass'))
                ]);
            }



            //Eliminamos gustos y preferencias anteriores e insertamos los nuevas:

            GustosGenero::where('id_usuario', '=', $r->get('id'))->delete();

            foreach ($r->gustosGenero as $val) {
                GustosGenero::create([
                    'id_usuario' => $r->get('id'),
                    'id_genero' => $val
                ]);
            }

            PrefUsuarios::where('id_usuario', '=', $r->get('id'))->delete();

            foreach ($r->preferencias as $val) {
                PrefUsuarios::create([
                    'id_usuario' => $r->get('id'),
                    'id_preferencia' => $val['id'],
                    'intensidad' => $val['intensidad']
                ]);
            }

            RolUsuario::where('id_usuario', '=', $r->get('id'))->delete();

            //Cambio 01-03-2022
            foreach ($r->roles as $rol) {
                RolUsuario::create([
                    'id_usuario' => $r->get('id'),
                    'id_rol' => $rol
                ]);
            }

            return response()->json(['mensaje' => 'Usuario actualizado correctamente'], 200);
        } catch (Exception $ex) {
            return response()->json(['mensaje' => 'No se ha podido crear el usuario, error interno del servidor. Mensaje de error: ' . $ex->getMessage()], 500);
        }
    }

    /**
     * Borra un usuario de la BBDD según el ID indicado
     * @param Integer $id
     * @return Response
     */
    public function borrarUsuario($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'No se encuentra el usuario'], 404);
        }
        $usuario->delete();
        return response()->json(['mensaje' => 'Usuario borrado correctamente'], 200);
    }

    /**
     * Activa o desactiva al usuario enviado por la petición
     * @param Request $r
     */
    public function togleActivado($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'No se encuentra el usuario'], 404);
        }
        $ac = $usuario->activo;
        $usuario->activo = ($usuario->activo == 1 ? 0 : 1);
        $usuario->save();
        return response()->json(['mensaje' => 'El usuario se ha ' . ($ac == 1 ? 'des' : '') . 'activado correctamente '], 200);
    }
}
