<?php

namespace Database\Factories;

use App\Models\PrefUsuarios;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrefUsuariosFactory extends Factory
{

    protected $model = PrefUsuarios::class;
    public static $idUsuario;
    public static $idPreferencia;
    public static $intensidad;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_usuario' => self::$idUsuario,
            'id_preferencia' => self::$idPreferencia,
            'intensidad' => self::$intensidad
        ];

    }
}
