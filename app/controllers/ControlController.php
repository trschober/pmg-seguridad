<?php

class ControlController extends BaseController{

	public function getIndex(){
		if(Input::has('institucion'))
			$valor_institucion = Input::get('institucion'); //experto al cambiar de institución
		else
			$valor_institucion = Session::has('sesion_institucion') ? Session::get('sesion_institucion') : Auth::user()->institucion_id; //experto con sesión o usuario de perfil reporte o validador
		
		if(Session::has('activo')){
			$controles = Control::with(array('comentarios' => function($query) use($valor_institucion){
		    	$query->where('institucion_id',$valor_institucion);
			}))->paginate(25);
		}else{
			$historial_id = Session::get('historial_id');
			$controles = Control::with(array('comentario_historial' => function($query) use($valor_institucion,$historial_id){
			    $query->where('institucion_id',$valor_institucion)->where('historial_id',$historial_id);
			}))->paginate(25);
		}
		$data['controles']=$controles;
		if(Auth::user()->perfil==='experto' || Auth::user()->perfil==='evaluador'){
			Session::put('sesion_institucion',$valor_institucion);
			$data['instituciones'] = \Helpers::getListadoInstituciones();
		}
		$data['habilitado'] = $this->getHabilitacion();
		$this->layout->title="Revisión de controles";
        $this->layout->content = Session::has('activo') ? View::make('controles/listado',$data) : View::make('controles/historial',$data);
	}

	public function getEstado(){
		if(Request::ajax()){
			$control_id = Input::get('control_id');
			if(Input::has('institucion'))
				$valor_institucion = Input::get('institucion'); //experto al cambiar de institución
			else
				$valor_institucion = Session::has('sesion_institucion') ? Session::get('sesion_institucion') : Auth::user()->institucion_id; //experto con sesión o usuario de perfil reporte o validador
			if(Session::has('activo')){
				$comentario = Comentario::where('institucion_id',$valor_institucion)->where('control_id',$control_id)->first();
				$archivos = Archivo::where('institucion_id',$valor_institucion)->where('control_id',$control_id)->get();
			}else{
				$comentario = ComentarioHistorial::where('institucion_id',$valor_institucion)->where('historial_id',Session::get('historial_id'))->where('control_id',$control_id)->first();
				$archivos = ArchivoHistorial::where('institucion_id',$valor_institucion)->where('historial_id',Session::get('historial_id'))->where('control_id',$control_id)->get();
			}
			$control = Control::find($control_id);
			if($comentario===null){
				return Response::json(array(
					'success'=>true,
				    'comentario'=>null
				));
			}else{
				return Response::json(array(
					'success'=>true,
				    'comentario'=>$comentario,
				    'archivos'=>$archivos,
				    'control'=>$control,
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
		$comentario->institucion_id = Auth::user()->institucion_id;
		$comentario->control_id = Input::get('control_id');
		if(Input::hasFile('archivo') && $comentario->cumple=='si'){
			$comentario->observaciones_institucion = null;
			$comentario->desc_medio_verificacion = Input::get('des_medios_ver');
			foreach(Input::file('archivo') as $file){				
				$archivo = new Archivo;
				$archivo->institucion_id=Auth::user()->institucion_id;
				$archivo->control_id=Input::get('control_id');
				$archivo_nombre = $file->getClientOriginalName();
				$archivo_nombre = \Helpers::cleanFileName($archivo_nombre);
				$archivo_nombre = Session::get('sesion_historial').'-'.Auth::user()->institucion_id.'-'.$control->id.'-'.$archivo_nombre;
				$archivo->filename=$archivo_nombre;
				$file->move('uploads/controles/'.Auth::user()->institucion_id.'/'.$control->id,$archivo_nombre);
				$archivo->save();
			}
		}else{
			$comentario->desc_medio_verificacion = NULL;
			$files = Archivo::where('institucion_id',Auth::user()->institucion_id)->where('control_id',Input::get('control_id'))->get();
			foreach ($files as $file) {
				$file->delete();
			}
		}
		$comentario->save();

		//Historial del ejercicio activo
		$comentario_historial = ComentarioHistorial::where('institucion_id',Auth::user()->institucion_id)->where('historial_id',Session::get('historial_id'))->where('control_id',Input::get('control_id'))->first();
		$control = Control::find(Input::get('control_id'));
		if($comentario_historial===null)
			$comentario_historial = new ComentarioHistorial;
		if(Input::has('cumplimiento'))
			$comentario_historial->cumple = Input::get('cumplimiento');
		if(Input::has('comentario_incumplimiento'))
			$comentario_historial->observaciones_institucion = Input::get('comentario_incumplimiento');
		$comentario_historial->anio_implementacion = Input::has('anio_implementacion') ? Input::get('anio_implementacion') : '-';
		$comentario_historial->institucion_id = Auth::user()->institucion_id;
		$comentario_historial->control_id = Input::get('control_id');
		$comentario_historial->historial_id = Session::get('historial_id');
		$comentario_historial->desc_medio_verificacion = Input::get('des_medios_ver');
		if(Input::hasFile('archivo') && $comentario->cumple=='si'){
			$comentario_historial->observaciones_institucion = null;
			foreach(Input::file('archivo') as $file){				
				$archivo = new ArchivoHistorial;
				$archivo->institucion_id=Auth::user()->institucion_id;
				$archivo->control_id=Input::get('control_id');
				$archivo_nombre = $file->getClientOriginalName();
				$archivo_nombre = \Helpers::cleanFileName($archivo_nombre);
				$archivo_nombre = Session::get('sesion_historial').'-'.Auth::user()->institucion_id.'-'.$control->id.'-'.$archivo_nombre;
				$archivo->filename=$archivo_nombre;
				$archivo->historial_id = Session::get('historial_id');
				$archivo->save();
			}
		}else{
			$comentario_historial->desc_medio_verificacion = NULL;
			$files = ArchivoHistorial::where('institucion_id',Auth::user()->institucion_id)->where('historial_id',Session::get('historial_id'))->where('control_id',Input::get('control_id'))->get();
			foreach ($files as $file) {
				$file->delete();
			}
		}
		$comentario_historial->save();

		return Response::json(['success' => true,'control'=>$control->id]);
	}

	public function getFile($archivo_id){
		if(in_array(Auth::user()->perfil,array('ingreso','validador'))){
			$archivo = Session::has('activo') ? Archivo::where('id',$archivo_id)->where('institucion_id',Auth::user()->institucion_id)->first() : ArchivoHistorial::where('id',$archivo_id)->where('historial_id',Session::get('historial_id'))->where('institucion_id',Auth::user()->institucion_id)->first();
			if($archivo!=null){
				return Response::download('uploads/controles/'.Auth::user()->institucion_id.'/'.$archivo->control_id.'/'.$archivo->filename);
			}
		}else{
			$archivo = Session::has('activo') ? Archivo::where('id',$archivo_id)->where('institucion_id',Session::get('sesion_institucion'))->first() : ArchivoHistorial::where('id',$archivo_id)->where('institucion_id',Session::get('sesion_institucion'))->where('historial_id',Session::get('historial_id'))->first();
			if($archivo!=null){
				return Response::download('uploads/controles/'.Session::get('sesion_institucion').'/'.$archivo->control_id.'/'.$archivo->filename);
			}
		}
	}

	public function deleteFile(){
		$habilitado = $this->getHabilitacion();
		if($habilitado){
			$archivo = Archivo::where('id',Input::get('archivo_id'))->where('institucion_id',Auth::user()->institucion_id)->first();
			if($archivo!=null){
				$control_id = $archivo->control_id;
				$archivo->delete();
				$cantidad_archivos = Archivo::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control_id)->where('deleted_at',NULL)->count();
				if($cantidad_archivos==0){
					$comentario = Comentario::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control_id)->first();
					$comentario->cumple=NULL;
					$comentario->anio_implementacion=NULL;
					$comentario->desc_medio_verificacion=NULL;
					$comentario->save();
				}

				//Historial
				$archivo = ArchivoHistorial::where('control_id',$control_id)->where('institucion_id',Auth::user()->institucion_id)->where('historial_id',Session::get('historial_id'))->where('filename',$archivo->filename)->first();
				if($archivo!=null){
					$control_id = $archivo->control_id;
					$archivo->delete();
					$cantidad_archivos = ArchivoHistorial::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control_id)->where('historial_id',Session::get('historial_id'))->where('deleted_at',NULL)->count();
					if($cantidad_archivos==0){
						$comentario = ComentarioHistorial::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control_id)->where('historial_id',Session::get('historial_id'))->first();
						$comentario->cumple=NULL;
						$comentario->anio_implementacion=NULL;
						$comentario->desc_medio_verificacion=NULL;
						$comentario->save();
					}					
				}

				return Response::json(['success' => true]);
			}else{
				return Response::json(['success' => false]);
			}
		}else{
			return Response::json(['success' => false]);
		}
	}

	public function setComentarioRed(){
		if(Input::has('institucion'))
			$valor_institucion = Input::get('institucion'); //experto al cambiar de institución
		else
			$valor_institucion = Session::has('sesion_institucion') ? Session::get('sesion_institucion') : Auth::user()->institucion_id; //experto con sesión o usuario de perfil reporte o validador
		$comentario = Comentario::where('institucion_id',$valor_institucion)->where('control_id',Input::get('control_experto'))->first();
		if($comentario!=NULL){
			if(Input::has('observaciones_expertos'))
				$comentario->observaciones_red = Input::get('observaciones_expertos');
			$comentario->save();
			return Response::json(['success' => true]);
		}
	}
}