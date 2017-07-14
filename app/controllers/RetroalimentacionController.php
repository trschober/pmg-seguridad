<?php

class RetroalimentacionController extends BaseController {
	
	public function index(){
		if(Input::has('institucion'))
			$valor_institucion = Input::get('institucion'); //experto al cambiar de institución
		else
			$valor_institucion = Session::has('sesion_institucion') ? Session::get('sesion_institucion') : Auth::user()->institucion_id; //experto con sesión o usuario de perfil reporte o validador

		if(Auth::user()->perfil==='experto'){
			$instituciones = Institucion::orderBy('servicio')->get();
			Session::put('sesion_institucion',$valor_institucion);
			$data['instituciones']=$instituciones;
		}
		$this->layout->title="Retroalimentación";
        $this->layout->content = View::make('retroalimentacion/inicio',$data);
	}
}