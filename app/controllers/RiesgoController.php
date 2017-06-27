<?php

class RiesgoController extends BaseController {

	public function index(){
		$data['riesgos'] = Riesgo::where('institucion_id',Auth::user()->institucion_id)->get();
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
}