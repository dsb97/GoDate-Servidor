<?php

use App\Http\Controllers\ControllerAdmin;
use App\Http\Controllers\ControllerUser;
use App\Http\Controllers\ControllerGeneric;
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

Route::group(['prefix' => 'user'], function () {
    Route::get('listarAfinidades/{idUsuario}', [ControllerUser::class, 'listarAfinidades']);
    Route::get('perfilUsuario/{idUsuario}', [ControllerUser::class, 'perfilUsuario']);
    Route::post('like', [ControllerUser::class, 'like']);
    Route::post('dislike', [ControllerUser::class, 'dislike']);
    Route::delete('borrarPerfil/{id}', [ControllerUser::class, 'borrarPerfil']);
    Route::get('listarAmigos/{idUsuario}', [ControllerUser::class, 'listarAmigos']);
    Route::get('listarGenteCerca/{idUsuario}', [ControllerUser::class, 'listarGenteCerca']);
    Route::get('listarLesGusto/{idUsuario}', [ControllerUser::class, 'listarLesGusto']);
    Route::get('cerrarSesion/{idUsuario}', [ControllerUser::class, 'cerrarSesion']);
});

Route::any('login', [ControllerGeneric::class, 'login']);
Route::get('listaCiudades', [ControllerGeneric::class, 'obtenerCiudadesFormulario']);
Route::any('subirFoto', [ControllerGeneric::class, 'subirFoto']);
Route::any('images/{file}', [ControllerGeneric::class, 'images']);

Route::group(['prefix' => 'admin'], function () {
    Route::post('altaUsuario', [ControllerAdmin::class, 'altaUsuario']);
    Route::get('listarUsuarios', [ControllerAdmin::class, 'listarUsuarios']);
    Route::get('detalleUsuario/{id}', [ControllerAdmin::class, 'detalleUsuario']);
    Route::post('actualizarUsuario', [ControllerAdmin::class, 'actualizarUsuario']);
    Route::delete('borrarUsuario/{id}', [ControllerAdmin::class, 'borrarUsuario']);

    Route::put('togleActivado/{id}', [ControllerAdmin::class, 'togleActivado']);
});
