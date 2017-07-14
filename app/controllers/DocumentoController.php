<?php

class DocumentoController extends BaseController {

	public function index(){
		$data['documentos'] = Documento::all();
		$this->layout->title= "Documentos";
    	$this->layout->content = View::make('documentos/inicio',$data);
	}

	public function formDocumento(){
		if(Auth::user()->perfil==='experto'){
			$this->layout->title= "Documentos";
	    	$this->layout->content = View::make('documentos/agregar');
    	}else{
    		return Redirect::to('bienvenida');
    	}
	}

	public function uploadDocumento(){
		if(Auth::user()->perfil==='experto'){
			$rules = array('archivo' => 'required');
			$file = array('archivo' => Input::file('archivo'));
			$validator = Validator::make($file, $rules);
			if ($validator->fails()) {
				Session::flash('error', 'Debe agregar un archivo');
				return Redirect::to('documentos/agregar');
			}else{
				$documento = new Documento;
				$extension = Input::file('archivo')->getClientOriginalExtension();
				$archivo_nombre = Input::file('archivo')->getClientOriginalName();
				$archivo_nombre = \Helpers::cleanFileName($archivo_nombre);
				Input::file('archivo')->move('public/uploads/documentos',$archivo_nombre);
				$documento->filename = $archivo_nombre;
				$documento->save();
				return Redirect::to('documentos');
			}
		}else{
			return Redirect::to('bienvenida');
		}
	}

	public function deleteFile($riesgo_id){
		if(Auth::user()->perfil==='experto'){
			$documento = Documento::where('id',$riesgo_id)->first();
			if($documento!=null){
				$documento->delete();
				unlink('public/uploads/documentos/'.$documento->filename);
			}
			return Redirect::to('documentos');
		}else{
			return Redirect::to('bienvenida');
		}
	}

	public function getFile($archivo){
		$documento = Documento::where('id',$archivo)->first();
		if($documento!=null)
			return Response::download('public/uploads/documentos/'.$documento->filename);
	}
}