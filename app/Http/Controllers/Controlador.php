<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class Controlador extends Controller
{
    public function inicio () {
        // $u = Usuario::with('genero')->get();
        // dd($u);
        return view('inicio');
    }
}
