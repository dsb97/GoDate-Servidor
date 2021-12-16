<?php

namespace Database\Factories;

use App\Models\Pass;
use Illuminate\Database\Eloquent\Factories\Factory;

class PassFactory extends Factory
{

    protected $model = Pass::class;

    public static $idUsuario;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_usuario' => self::$idUsuario,
            'pass' => md5('Asd12345')
        ];
    }
}
