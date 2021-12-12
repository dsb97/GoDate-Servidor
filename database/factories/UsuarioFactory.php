<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{

    protected $model = Usuario::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->firstName,
            'apellidos' => $this->faker->lastName,
            'correo' =>preg_replace('/@example\..*/', '@mail.com', $this->faker->unique()->safeEmail),
            'pass' => md5('12345'),
            'id_genero' => rand(1,3),
            'fecha_nacimiento' => $this->faker->date,
            'ciudad' => $this->faker->city,
            'descripcion' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Voluptate alias omnis repellat numquam odio sapiente, suscipit quo asperiores, eveniet nostrum quasi.',
            'foto' => 'https://picsum.photos/350',
            'conectado' => rand(0,1),
            'activo' => rand(0,1),
            'tema' => rand(0,1),
            'hijos' => rand(0,6)
        ];
    }
}
