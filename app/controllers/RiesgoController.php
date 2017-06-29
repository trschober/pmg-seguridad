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
		$data['riesgos'] = Riesgo::where('institucion_id',$valor_institucion)->get();
		$this->layout->title= "Análisis de riesgos";
    	$this->layout->content = View::make('riesgos/inicio',$data);
	}

	public function setFile(){
		$riesgo = new Riesgo;
		$riesgo->institucion_id=Auth::user()->institucion_id;
		$extension = Input::file('archivo')->getClientOriginalExtension();
		$archivo_nombre = Input::file('archivo')->getClientOriginalName();
		$archivo_nombre = \Helpers::cleanFileName($archivo_nombre);
		Input::file('archivo')->move('public/uploads/riesgos/'.Auth::user()->institucion_id,$archivo_nombre);
		$riesgo->filename = $archivo_nombre;
		$riesgo->save();
		return Redirect::to('riesgos');
	}

	public function deleteFile($riesgo_id){
		$riesgo = Riesgo::where('institucion_id',Auth::user()->institucion_id)->where('id',$riesgo_id)->first();
		if($riesgo!=null){
			$riesgo->delete();
			$files = glob('public/uploads/riesgos/'.Auth::user()->institucion_id.'/*');
			foreach($files as $file){
			  if(is_file($file))
			    unlink($file);
			}
		}
		return Redirect::to('riesgos');
	}

	public function getFile($archivo){
		$riesgo = Riesgo::where('institucion_id',Auth::user()->institucion_id)->where('id',$archivo)->first();
		if($riesgo!=null)
			return Response::download('public/uploads/riesgos/'.Auth::user()->institucion_id.'/'.$riesgo->filename);
	}
}