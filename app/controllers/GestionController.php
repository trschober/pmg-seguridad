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

		$data['usuarios'] = Usuario::with('institucion')->where('institucion_id',$valor_institucion)->get();
		$data['instituciones'] = \Helpers::getListadoInstituciones();
		$this->layout->title="Usuarios";
        $this->layout->content = View::make('gestion/listado_usuarios',$data);
	}

	public function getUsuarioDetalle($usuario_id = NULL){
		$usuario = Usuario::where('id',$usuario_id)->first();
		$data['usuario'] = $usuario!=null ? $usuario : new Usuario();
		$data['instituciones'] = \Helpers::getListadoInstituciones();
		$this->layout->title="Usuarios";
        $this->layout->content = View::make('gestion/editar_usuario',$data);
	}

	public function updateUsuario(){
		$reglas =  array(
            'rut'  => 'required','min:8',
            'nombres' => 'required',
            'apellidos' => 'required',
            'correo' => 'required|email',
            'perfil' => 'required',
            'institucion_usuario' => 'required'
        );
        $respuesta = Validator::make(Input::all(),$reglas);
        if ($respuesta->fails()) {
			return Redirect::to('gestion/usuarios/editar')->withErrors($respuesta)->withInput();
		}else{
			$rut = str_replace(".", "",trim(Input::get('rut')));
			$usuario = Usuario::where('rut',$rut)->first();
			if($usuario===null)
				$usuario = new Usuario();

    	 	$usuario->rut = $rut;
    	 	$usuario->nombres = Input::get('nombres');
    	 	$usuario->apellidos = Input::get('apellidos');
    	 	$usuario->correo = Input::get('correo');
    	 	$usuario->perfil = Input::get('perfil');
    	 	$usuario->institucion_id = Input::get('institucion_usuario');
    	 	$usuario->save();
    	 	Session::flash('usuario_ok','Usuario generado exitósamente');
    	 	return Redirect::to('gestion/usuarios');
		}
	}
}