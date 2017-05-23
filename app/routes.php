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

Route::get('/', function()
{
	return View::make('hello');
});

//Controles
Route::get('controles/index', 'ControlController@getIndex');
Route::get('controles/estado', 'ControlController@getEstado');
Route::post('controles/incumplimiento', 'ControlController@setIncumplimiento');