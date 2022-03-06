<?php

namespace App\Http\Controllers;

use App\Models\Preferencia;
use App\Models\Ciudad;
use App\Models\GustosGenero;
use App\Models\Usuario;
use App\Models\Pass;
use App\Models\PrefUsuarios;
use App\Models\RolUsuario;
use Exception;
use Illuminate\Http\Request;

class ControllerGeneric extends Controller
{
    /**
     * Función que comprueba la existencia de un usuario según el correo y la contraseña
     * asignada por parámetros en la petición
     */
    public function login(Request $r)
    {
        try {
            $correo = $r->get('correo');
            $pass = $r->get('pass');

            $u = Usuario::where([
                ['correo', '=', $correo],
                //['activo', '=', 1]
            ])->get()[0];
            $p = Pass::where('id_usuario', $u->id)->get()[0];
            if ($u) {
                if ($u->activo == 1) {
                    if ($p->pass == md5($pass)) {
                        $rr = Usuario::where('correo', $correo)->get(['id', 'correo', 'nombre', 'apellidos', 'foto'])[0];
                        //Cambio 27-02-2022: Incorporación de roles en la respuesta
                        $roles = RolUsuario::where('id_usuario', '=', $rr->id)
                            ->get()->pluck('id_rol')->toArray();
                        $rr->roles = $roles;
                        $u->conectado = 1;
                        $u->save();
                        return response()->json($rr, 200);
                    } else {
                        return response()->json(['mensaje' => 'Contraseña incorrecta'], 403);
                    }
                } else {
                    return response()->json(['mensaje' => 'Este usuario aún no está activo.'], 403);
                }
            } else {
                return response()->json(['mensaje' => 'Este usuario no está registrado'], 404);
            }
        } catch (Exception $ex) {
            return response()->json(['mensaje' => 'Este usuario no está registrado en el sistema'], 500);
        }
    }

    /**
     * Registra un nuevo usuario en la aplicación
     * @param Request $r
     * @return Response
     */
    public function registrar(Request $r)
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
                // 'foto' => $r->get('foto'),
                'foto' => 'https://picsum.photos/350',
                'hijos' => $r->get('hijos'),
                'conectado' => 0,
                'activo' => 0,
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

            return response()->json(['mensaje' => 'Usuario registrado correctamente'], 200);
        } catch (Exception $ex) {
            $u = Usuario::find($id);

            if ($u) {
                $u->delete();
            }

            return response()->json(['mensaje' => 'No se ha podido crear el usuario, error interno del servidor'], 500);
        }
    }

    /**
     * Obtiene las ciudades para el select del formulario de registro
     */
    public function obtenerCiudadesFormulario()
    {
        return response()->json(Ciudad::orderBy('ciudad', 'asc')->get()->pluck('ciudad'), 200);
    }

    /**
     * Obtiene las preferencias para el select del formulario de registro
     */
    public function obtenerPreferenciasFormulario()
    {
        return response()->json(Preferencia::get(['id', 'descripcion']), 200);
    }

    public function subirFoto(Request $r)
    {
        $folderPath = public_path() . DIRECTORY_SEPARATOR . 'imagenes/';

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $file_tmp = $_FILES['file']['tmp_name'];
        $file_ext = explode('/', $_FILES['file']['type'] . '')[1];
        $file = uniqid() . '.' . $file_ext;
        $filePath = $folderPath . $file;
        move_uploaded_file($file_tmp, $filePath);

        $respuesta = $r->getSchemeAndHttpHost() . '/api/images/' . $file;
        error_log($respuesta);
        return response()->json(['mensaje' => $respuesta], 200);
    }

    /**
     * Obtiene las imagenes en formato file
     * Esto me ha llevado 5 horas de un día que podría haber dedicado a otra cosa,
     * pero bueno, pasas que cosan
     */
    public function images($file) {
        $folderPath = public_path() . DIRECTORY_SEPARATOR . 'imagenes/';
        $filew = $folderPath . $file;
        return response()->file($filew);

    }
}
