<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Inicio
Route::get('/', 'HomeController@showWelcome');
//Claveunica
Route::post('claveunica/autenticar', 'ClaveUnicaController@autenticar');
Route::get('claveunica/validar', 'ClaveUnicaController@validar');
Route::get('login', 'ClaveUnicaController@login');
Route::get('logout', 'ClaveUnicaController@logout');

Route::group(["before" => "auth"], function() {
	//Controles
	Route::get('controles', 'ControlController@getIndex');
	Route::get('controles/estado', 'ControlController@getEstado');
	Route::post('controles/actualizar', 'ControlController@actualizarControl');
	Route::get('controles/carga', 'ControlController@cargaPlanilla');
	Route::post('controles/upload', 'ControlController@uploadPlanilla');
});