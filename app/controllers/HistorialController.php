<?php

class HistorialController extends BaseController {

	public function index(){
		if(Auth::user()->perfil==='evaluador'){
			$historial = HistorialEjercicio::where('activo',1)->first();
			Session::put('activo',$historial->activo);
			return Redirect::to('controles');
		}
			
		$data['historial_ejercicios'] = HistorialEjercicio::orderBy('id','DESC')->get();
		$this->layout->title= "Historial de ejercicios";
    	$this->layout->content = View::make('historial_ejercicios/inicio',$data);
	}

	public function elegirEjercicio($historial_id = null){
		$historial = HistorialEjercicio::find($historial_id);
		Session::put('proceso_tipo',$historial->tipo);
		Session::put('historial_id',$historial->id);
		Session::put('sesion_historial',strtoupper($historial->anio.'-'.$historial->tipo));
		if($historial->activo)
			Session::put('activo',$historial->activo);
		else
			Session::forget('activo');

		return Redirect::to('bienvenida');
	}
}