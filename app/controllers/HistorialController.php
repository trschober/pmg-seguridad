<?php

class HistorialController extends BaseController {

	public function index(){
		$data['historial_ejercicios'] = HistorialEjercicio::all();
		$this->layout->title= "Historial de ejercicios";
    	$this->layout->content = View::make('historial_ejercicios/inicio',$data);
	}

	public function elegirEjercicio(){
		$historial = HistorialEjercicio::find(Input::get('historial'));
		if(!$historial->en_curso)
			Session::put('sesion_historial',$historial->id);
		else
			Session::forget('sesion_historial');
		return Redirect::to('controles');
	}
	
}