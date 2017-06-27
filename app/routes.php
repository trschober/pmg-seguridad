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

//Home
Route::get('/', 'HomeController@index');

//Claveunica
Route::post('claveunica/autenticar', 'ClaveUnicaController@autenticar');
Route::get('claveunica/validar', 'ClaveUnicaController@validar');
Route::get('login', 'ClaveUnicaController@login');
Route::get('logout', 'ClaveUnicaController@logout');

Route::group(["before" => "auth"], function() {
	//Home
	Route::get('bienvenida', 'HomeController@bienvenida');
	
	//Controles
	Route::get('controles', 'ControlController@getIndex');
	Route::post('controles', 'ControlController@getIndex');
	Route::get('controles/estado', 'ControlController@getEstado');
	Route::post('controles/actualizar', 'ControlController@actualizarControl');
	Route::post('controles/red', 'ControlController@setComentarioRed');
	Route::get('controles/carga', 'ControlController@cargaPlanilla');
	Route::post('controles/upload', 'ControlController@uploadPlanilla');
	Route::get('controles/download/{archivo}', 'ControlController@getFile');
	Route::get('controles/archivo/eliminar', 'ControlController@deleteFile');

	//Instituciones
	Route::get('institucion/aprobar', 'InstitucionController@setAprobacion');
	Route::post('institucion/rechazar', 'InstitucionController@setRechazo');
	Route::get('institucion/cerrar', 'InstitucionController@setCierre');

	//Riesgos
	Route::get('riesgos','RiesgoController@index');
	Route::post('riesgos/agregar','RiesgoController@setFile');
	Route::get('riesgos/eliminar/{riesgo_id}','RiesgoController@deleteFile');
});