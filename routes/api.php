<?php

use App\Http\Controllers\ControllerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['prefix'=>'user'], function() {
    Route::get('listarAfinidades/{idUsuario}', [ControllerUser::class, 'listarAfinidades']);
    Route::get('perfilUsuario/{idUsuario}', [ControllerUser::class, 'perfilUsuario']);
    Route::post('like/{id}', [ControllerUser::class, 'like']);
    Route::post('dislike/{id}', [ControllerUser::class, 'dislike']);
    Route::delete('borrarPerfil/{id}', [ControllerUser::class, 'borrarPerfil']);
});

//Posible modificación
Route::get('iniciarSesion', [Controller::class, 'iniciarSesion']);

//get: listar
//post: registrar
//put: modificar
//delete:borrar
