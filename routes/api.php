<?php

use App\Http\Controllers\ControllerAdmin;
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
Route::group(['prefix' => 'user'], function () {
    Route::get('listarAfinidades/{idUsuario}', [ControllerUser::class, 'listarAfinidades']);
    Route::get('perfilUsuario/{idUsuario}', [ControllerUser::class, 'perfilUsuario']);
    Route::post('like', [ControllerUser::class, 'like']);
    Route::post('dislike', [ControllerUser::class, 'dislike']);
    Route::delete('borrarPerfil/{id}', [ControllerUser::class, 'borrarPerfil']);
    Route::get('listarAmigos/{idUsuario}', [ControllerUser::class, 'listarAmigos']);
    Route::get('listarGenteCerca/{idUsuario}', [ControllerUser::class, 'listarGenteCerca']);
    Route::get('listarLesGusto/{idUsuario}', [ControllerUser::class, 'listarLesGusto']);
});
Route::get('iniciarSesion', [ControllerGeneric::class, 'iniciarSesion']);

Route::group(['prefix' => 'admin'], function () {
    Route::get('listarUsuarios', [ControllerAdmin::class, 'listarUsuarios']);
    Route::post('altaUsuario', [ControllerAdmin::class, 'altaUsuario']);
    Route::put('togleActivado', [ControllerAdmin::class, 'togleActivado']);
    Route::delete('borrarUsuario', [ControllerAdmin::class, 'borrarUsuario']);
});
//get: listar
//post: registrar
//put: modificar
//delete:borrar
/**
 * url
 * let headers = new HttpHeaders({
 *  'Content-Type' : 'application/json'
 * });
 *
 * return this.http.post(url, [email1, email2], {headers: headers})
 */
