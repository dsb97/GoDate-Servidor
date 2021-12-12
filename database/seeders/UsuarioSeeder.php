<?php

namespace Database\Seeders;

use App\Models\GustosGenero;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Pass;
use App\Models\Preferencia;
use App\Models\PrefUsuarios;
use Database\Factories\GustosGeneroFactory;
use Database\Factories\PassFactory;
use Database\Factories\PreferenciasUsuariosFactory;
use Database\Factories\PrefUsuariosFactory;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 5; $i++) {

            //Creamos el usuario
            Usuario::factory()->create();
            $idUsuario = Usuario::max('id');
            //Creamos su contraseña
            PassFactory::$idUsuario = $idUsuario;
            Pass::factory()->create();
            //Establecemos las preferencias para cada usuario
            PrefUsuariosFactory::$idUsuario = $idUsuario;
            $preferencias = Preferencia::all();
            for ($j = 0; $j < count($preferencias); $j++) {
                $intensidad = 0;
                if (
                    str_contains($preferencias[$j]->descripcion, 'Relaci')
                    || str_contains($preferencias[$j]->descripcion, 'Hijo')
                ) {
                    $intensidad = rand(0, 1) * 100;
                } else {
                    $intensidad = rand(0, 100);
                }
                PrefUsuariosFactory::$idPreferencia = $preferencias[$j]->id;
                PrefUsuariosFactory::$intensidad = $intensidad;
                PrefUsuarios::factory()->create();
            }

            //Establecemos las preferencias de género para cada usuario
            //1: H, 2: M, 3: NB, 4: H-M, 5: H-NB, 6: M-NB, 7: H-M-NB
            GustosGeneroFactory::$idUsuario = $idUsuario;
            $g = rand(1, 7);
            switch ($g) {
                case 1:
                case 2:
                case 3:
                    GustosGeneroFactory::$idGenero = $g;
                    GustosGenero::factory()->create();
                    break;
                case 4:
                    GustosGeneroFactory::$idGenero = 1;
                    GustosGenero::factory()->create();
                    GustosGeneroFactory::$idGenero = 2;
                    GustosGenero::factory()->create();
                    break;
                case 5:
                    GustosGeneroFactory::$idGenero = 1;
                    GustosGenero::factory()->create();
                    GustosGeneroFactory::$idGenero = 3;
                    GustosGenero::factory()->create();
                    break;
                case 6:
                    GustosGeneroFactory::$idGenero = 3;
                    GustosGenero::factory()->create();
                    GustosGeneroFactory::$idGenero = 2;
                    GustosGenero::factory()->create();
                    break;
                case 7:
                    GustosGeneroFactory::$idGenero = 1;
                    GustosGenero::factory()->create();
                    GustosGeneroFactory::$idGenero = 2;
                    GustosGenero::factory()->create();
                    GustosGeneroFactory::$idGenero = 3;
                    GustosGenero::factory()->create();
                    break;
            }
        }
    }
}
