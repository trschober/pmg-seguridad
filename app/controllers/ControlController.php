<?php

class ControlController extends BaseController{

	public function getIndex(){
		if(Input::has('institucion'))
			$valor_institucion = Input::get('institucion'); //experto al cambiar de institución
		else
			$valor_institucion = Session::has('sesion_institucion') ? Session::get('sesion_institucion') : Auth::user()->institucion_id; //experto con sesión o usuario de perfil reporte o aprobador
		$controles = Control::with(array('comentarios' => function($query) use($valor_institucion){
			    $query->where('institucion_id',$valor_institucion);
			}))->paginate(10);
		$data['controles']=$controles;
		if(Auth::user()->perfil==='expertos'){
			$instituciones = Institucion::orderBy('servicio')->get();
			Session::put('sesion_institucion',$valor_institucion);
			$data['instituciones']=$instituciones;
		}
		$this->layout->title="Revisión de controles";
        $this->layout->content = View::make('controles/listado',$data);
	}

	public function getEstado(){
		if(Request::ajax()){
			$control_id = Input::get('control_id');
			$comentario = Comentario::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control_id)->first();
			$archivos = Archivo::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control_id)->get();
			if($comentario===null){
				return Response::json(array(
					'success'=>true,
				    'comentario'=>null
				));
			}else{
				return Response::json(array(
					'success'=>true,
				    'comentario'=>$comentario,
				    'archivos'=>$archivos
				));
			}
		}
	}

	public function actualizarControl(){
		$comentario = Comentario::where('institucion_id',Auth::user()->institucion_id)->where('control_id',Input::get('control_id'))->first();
		$control = Control::find(Input::get('control_id'));
		if($comentario===null)
			$comentario = new Comentario;
		if(Input::has('cumplimiento'))
			$comentario->cumple = Input::get('cumplimiento');
		if(Input::has('comentario_incumplimiento'))
			$comentario->observaciones_institucion = Input::get('comentario_incumplimiento');
		$comentario->anio_implementacion = Input::has('anio_implementacion') ? Input::get('anio_implementacion') : '-';
		//echo $comentario->anio_implementacion;
		$comentario->institucion_id = Auth::user()->institucion_id;
		$comentario->control_id = Input::get('control_id');
		if(Input::hasFile('archivo')){
			$comentario->observaciones_institucion = null;
			$comentario->cumple = 'si';
			foreach(Input::file('archivo') as $file){				
				$archivo = new Archivo;
				$archivo->institucion_id=Auth::user()->institucion_id;
				$archivo->control_id=Input::get('control_id');
				$archivo_nombre = $file->getClientOriginalName();
				$archivo_nombre = str_replace(" ","-",$archivo_nombre);
				$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
				$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
				$archivo_nombre = str_replace($no_permitidas, $permitidas ,$archivo_nombre);
				$archivo_nombre = strtolower($archivo_nombre);
				$archivo->filename=$archivo_nombre;
				$file->move('public/uploads/'.Auth::user()->institucion_id.'/'.$control->id,$archivo_nombre);
				$archivo->save();
			}
		}else{
			$files = Archivo::where('institucion_id',Auth::user()->institucion_id)->where('control_id',Input::get('control_id'))->get();
			foreach ($files as $file) {
				$file->delete();
			}
			$files = glob('public/uploads/'.Auth::user()->institucion_id.'/'.$control->id.'/*');
			foreach($files as $file){
			  if(is_file($file))
			    unlink($file);
			}
		}
		$comentario->save();
		return Response::json(['success' => true,'control'=>$control->id]);
	}

	public function cargaPlanilla(){
		$this->layout->title= "Carga de controles";
        $this->layout->content = View::make('controles/cargar');
	}
	//carga de controles por planilla
	public function uploadPlanilla(){
		$file = array('excel' => Input::file('excel'));
		$rules = array('excel' => 'required',);
		$validator = Validator::make($file, $rules);
		if ($validator->fails()) {
			return Redirect::to('controles/carga')->withInput()->withErrors($validator);
		}
		else {
			if (Input::file('excel')->isValid()) {
				$destinationPath = 'public/uploads';
				$extension = Input::file('excel')->getClientOriginalExtension();
				$name = Input::file('excel')->getClientOriginalName();
				$fileName = $name;
				Input::file('excel')->move($destinationPath, $fileName);

				//Cargar excel en tabla
				$archivo = $destinationPath."/".$fileName;
				$inputFileType = PHPExcel_IOFactory::identify($archivo);
				$objReader= PHPExcel_IOFactory::createReader($inputFileType);
				$objReader->setReadDataOnly(true);
				$objPHPExcel=$objReader->load($archivo);
				$objWorksheet = $objPHPExcel->getActiveSheet();
				$rows = $objPHPExcel->getActiveSheet()->getHighestRow();
				for($fila=2;$fila<=$rows;$fila++){
					$institucion = Institucion::where('servicio',$objWorksheet->getCellByColumnAndRow(1, $fila)->getValue())->first();
					$control = Control::where('codigo',$objWorksheet->getCellByColumnAndRow(3, $fila)->getValue())->first();
					if(!is_null($institucion) && !is_null($control) ){
						$comentario = new Comentario();
						$comentario->institucion_id = $institucion->id;
						$comentario->control_id = $control->id;
						$comentario->anio_compromiso = $objWorksheet->getCellByColumnAndRow(4, $fila)->getValue();
						$comentario->cumple = null;
						$comentario->save();
					}else{
						echo $objWorksheet->getCellByColumnAndRow(1, $fila)->getValue()."---".$objWorksheet->getCellByColumnAndRow(3, $fila)->getValue()."<br>";
					}
				}
				Session::flash('success', 'Carga exitosa');
				return Redirect::to('controles/carga');
			}else {
				Session::flash('error', 'Archivo invalido');
				return Redirect::to('controles/carga');
			}
		}
	}

	public function getFile($archivo_id){
		$archivo = Archivo::where('id',$archivo_id)->where('institucion_id',Auth::user()->institucion_id)->first();
		if($archivo!=null){
			return Response::download('public/uploads/'.Auth::user()->institucion_id.'/'.$archivo->control_id.'/'.$archivo->filename);
		}
	}

	public function deleteFile(){
		$archivo = Archivo::where('id',Input::get('archivo_id'))->where('institucion_id',Auth::user()->institucion_id)->first();
		if($archivo!=null){
			$control_id = $archivo->control_id;
			$archivo->delete();
			$cantidad_archivos = Archivo::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control_id)->where('deleted_at',NULL)->count();
			if($cantidad_archivos==0){
				$comentario = Comentario::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control_id)->first();
				$comentario->cumple=NULL;
				$comentario->anio_implementacion=NULL;
				$comentario->save();
			}
			return Response::json(['success' => true]);
		}else{
			return Response::json(['success' => false]);
		}
	}
}