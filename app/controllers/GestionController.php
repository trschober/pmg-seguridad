<?php

class GestionController extends BaseController {

	public function getInstituciones(){
		$data['instituciones'] = \Helpers::getListadoInstituciones();
		$this->layout->title="Instituciones";
        $this->layout->content = View::make('gestion/instituciones',$data);
	}

	public function updateInstitucion(){
		if(Auth::user()->perfil==='experto'){
			$institucion = Institucion::find(Input::get('institucion_id'));
			$institucion->estado = Input::get('estado');
			$institucion->save();
			return Response::json(['success' => true]);
		}
		return Response::json(['success' => false]);
	}

	public function getUsuarios(){
		if(Input::has('institucion'))
			$valor_institucion = Input::get('institucion'); //experto al cambiar de institución
		else
			$valor_institucion = Session::has('sesion_institucion') ? Session::get('sesion_institucion') : Auth::user()->institucion_id; //experto con sesión o usuario de perfil reporte o validador

		$data['usuarios'] = Usuario::with('institucion')->participantes()->where('institucion_id',$valor_institucion)->get();
		$data['instituciones'] = \Helpers::getListadoInstituciones();
		$this->layout->title="Usuarios";
        $this->layout->content = View::make('gestion/usuarios',$data);
	}

	public function getUsuarioDetalle(){
		$usuario = Usuario::where('id',Input::get('usuario_id'))->first();
		if($usuario!=null){
			return Response::json(['success' => true,'usuario'=>$usuario]);
		}
	}

	public function updateUsuario(){
		
	}
}