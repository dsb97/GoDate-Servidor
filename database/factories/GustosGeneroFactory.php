<?php

namespace Database\Factories;

use App\Models\GustosGenero;
use Illuminate\Database\Eloquent\Factories\Factory;

class GustosGeneroFactory extends Factory
{
    protected $model = GustosGenero::class;
    public static $idUsuario;
    public static $idGenero;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_usuario' => self::$idUsuario,
            'id_genero' => self::$idGenero
        ];
    }
}
