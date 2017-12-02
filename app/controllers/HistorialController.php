<?php

class HistorialController extends BaseController {

	public function index(){
		$data['historial_ejercicios'] = HistorialEjercicio::all();
		$this->layout->title= "Historial de ejercicios";
    	$this->layout->content = View::make('historial_ejercicios/inicio',$data);
	}

	public function elegirEjercicio(){
		$historial = HistorialEjercicio::find(Input::get('historial'));
		Session::put('historial_id',$historial->id);
		Session::put('sesion_historial',$historial->anio.'-'.$historial->tipo);
		if($historial->activo)
			Session::put('activo',$historial->activo);
		else
			Session::forget('activo');
		return Redirect::to('controles');
	}
}