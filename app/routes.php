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
Route::get('/', function(){
	if(\Auth::check()){
		return \Redirect::to('historial');
	}else{
        return \Redirect::to('portada');
	}
});
Route::get('portada', 'HomeController@index');
//Claveunica
Route::post('claveunica/autenticar', 'ClaveUnicaController@autenticar');
Route::get('claveunica/validar', 'ClaveUnicaController@validar');
Route::get('login', 'ClaveUnicaController@login');
Route::get('logout', 'ClaveUnicaController@logout');

Route::group(["before" => "auth"], function() {
	//Home
	Route::get('bienvenida', 'HomeController@bienvenida');

	//Historial
	Route::get('historial', 'HistorialController@index');
	Route::post('historial', 'HistorialController@elegirEjercicio');
	
	//Controles
	Route::get('controles', 'ControlController@getIndex');
	Route::post('controles', 'ControlController@getIndex');
	Route::get('controles/estado', 'ControlController@getEstado');
	Route::post('controles/actualizar', 'ControlController@actualizarControl');
	Route::post('controles/red', 'ControlController@setComentarioRed');
	Route::get('controles/download/{archivo}', 'ControlController@getFile');
	Route::get('controles/archivo/eliminar', 'ControlController@deleteFile');

	//Instituciones
	Route::get('institucion/aprobar', 'InstitucionController@setAprobacion');
	Route::post('institucion/rechazar', 'InstitucionController@setRechazo');
	Route::get('institucion/cerrar', 'InstitucionController@setCierre');
	Route::get('institucion/informe-cierre', 'InstitucionController@getReporteCierre');
	Route::get('institucion/reporte', 'InstitucionController@reporteCierre');

	//Riesgos
	Route::get('riesgos','RiesgoController@index');
	Route::post('riesgos','RiesgoController@index');
	Route::post('riesgos/agregar','RiesgoController@setFile');
	Route::get('riesgos/eliminar/{riesgo_id}','RiesgoController@deleteFile');
	Route::get('riesgos/download/{archivo}', 'RiesgoController@getFile');

	//Gestión
	Route::get('gestion/instituciones','GestionController@getInstituciones');
	Route::get('gestion/instituciones/exportar','GestionController@institucionesExportar');
	Route::get('gestion/instituciones/actualizar','GestionController@updateInstitucion');
	Route::get('gestion/usuarios','GestionController@getUsuarios');
	Route::post('gestion/usuarios','GestionController@getUsuarios');
	Route::get('gestion/usuarios/editar/{usuario_id?}','GestionController@getUsuarioDetalle');
	Route::post('gestion/usuarios/actualizar','GestionController@updateUsuario');
	Route::get('gestion/usuarios/eliminar/{usuario_id?}','GestionController@deleteUsuario');
	Route::get('gestion/detalle/exportar','GestionController@detalleInstitucionesExportar');
	Route::get('gestion/informes/exportar','GestionController@exportarInformes');

	//Documentos
	Route::get('documentos','DocumentoController@index');
	Route::get('documentos/agregar','DocumentoController@formDocumento');
	Route::post('documentos/upload','DocumentoController@uploadDocumento');
	Route::get('documentos/eliminar/{documento_id}','DocumentoController@deleteFile');
	Route::get('documentos/download/{documento_id}', 'DocumentoController@getFile');

	//Retroalimentación
	Route::get('retroalimentacion','RetroalimentacionController@index');
	Route::post('retroalimentacion','RetroalimentacionController@index');
	Route::post('retroalimentacion/observaciones','RetroalimentacionController@setObservacionesRed');
	Route::get('retroalimentacion/reporte','RetroalimentacionController@setReporteRed');
	Route::get('retroalimentacion/resultado','RetroalimentacionController@getReporteRed');
});