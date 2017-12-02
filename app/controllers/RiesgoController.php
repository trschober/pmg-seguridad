<?php

class RiesgoController extends BaseController {

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
		$data['riesgos'] = Session::has('activo') ? Riesgo::where('institucion_id',$valor_institucion)->get() : RiesgoHistorial::where('institucion_id',$valor_institucion)->where('historial_id',Session::get('historial_id'))->get();
		$data['habilitado'] = $this->getHabilitacion();
		$this->layout->title= "Análisis de riesgos";
    	$this->layout->content = Session::has('activo') ? View::make('riesgos/inicio',$data) : View::make('riesgos/historial',$data);
	}

	public function setFile(){
		$riesgo = new Riesgo;
		$riesgo->institucion_id=Auth::user()->institucion_id;
		$extension = Input::file('archivo')->getClientOriginalExtension();
		$archivo_nombre = Input::file('archivo')->getClientOriginalName();
		$archivo_nombre = \Helpers::cleanFileName($archivo_nombre);
		Input::file('archivo')->move('uploads/riesgos/'.Auth::user()->institucion_id,$archivo_nombre);
		$riesgo->filename = $archivo_nombre;
		$riesgo->save();
		
		//Historial
		$riesgo = new RiesgoHistorial;
		$riesgo->institucion_id=Auth::user()->institucion_id;
		$extension = Input::file('archivo')->getClientOriginalExtension();
		$archivo_nombre = Input::file('archivo')->getClientOriginalName();
		$archivo_nombre = \Helpers::cleanFileName($archivo_nombre);
		$riesgo->historial_id = Session::get('historial_id');
		$riesgo->filename = $archivo_nombre;
		$riesgo->save();

		return Redirect::to('riesgos');
	}

	public function deleteFile($riesgo_id){
		$riesgo = Riesgo::where('institucion_id',Auth::user()->institucion_id)->where('id',$riesgo_id)->first();
		if($riesgo!=null){
			$riesgo->delete();
			$riesgoh = RiesgoHistorial::where('institucion_id',Auth::user()->institucion_id)->where('historial_id',Session::get('historial_id'))->where('filename',$riesgo->filename)->first();
			$riesgoh->delete();
			unlink('uploads/riesgos/'.Auth::user()->institucion_id.'/'.$riesgo->filename);
		}
		return Redirect::to('riesgos');
	}

	public function getFile($archivo){
		if(Auth::user()->perfil!='experto'){
			$riesgo = Session::has('activo') ? Riesgo::where('institucion_id',Auth::user()->institucion_id)->where('id',$archivo)->first() : RiesgoHistorial::where('institucion_id',Auth::user()->institucion_id)->where('historial_id',Session::get('historial_id'))->where('id',$archivo)->first();
			if($riesgo!=null)
				return Response::download('uploads/riesgos/'.Auth::user()->institucion_id.'/'.$riesgo->filename);
		}else{
			$riesgo = Session::has('activo') ? Riesgo::where('institucion_id',Session::get('sesion_institucion'))->where('id',$archivo)->first() : RiesgoHistorial::where('institucion_id',Session::get('sesion_institucion'))->where('historial_id',Session::get('historial_id'))->where('id',$archivo)->first();
			if($riesgo!=null)
				return Response::download('uploads/riesgos/'.Session::get('sesion_institucion').'/'.$riesgo->filename);
		}
	}
}