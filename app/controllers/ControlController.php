<?php

class ControlController extends BaseController{

	public function getIndex(){
		$controles = Control::with(array('comentarios' => function( $query ){
		    $query->where('institucion_id',Auth::user()->institucion_id);
		}))->paginate(10);
		$this->layout->title= "Revisión de controles";
        $this->layout->content = View::make('controles/listado', array('controles'=>$controles));
	}

	public function getEstado(){
		if(Request::ajax()){
			$control_id = Input::get('control_id');
			$comentario = Comentario::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control_id)->first();
			//$archivos = Files::where('institucion_id',1)->where('control_id',$control_id)->get();
			if($comentario===null){
				return Response::json(array(
					'success'=>true,
				    'comentario'=>null
				));
			}else{
				return Response::json(array(
					'success'=>true,
				    'comentario'=>$comentario
				));
			}
		}
	}

	public function actualizarControl(){
		$comentario = Comentario::where('institucion_id',Auth::user()->institucion_id)->where('control_id',Input::get('control_id'))->first();
		$control = Control::find(Input::get('control_id'));
		if($comentario===null)
			$comentario = new Comentario;
		if(Input::has('cumple'))
			$comentario->cumple = Input::get('cumple');
		if(Input::has('comentario'))
			$comentario->observaciones_institucion = Input::get('comentario');
		if(Input::has('anio'))
			$comentario->anio_implementacion = Input::get('anio');
		$comentario->institucion_id = Auth::user()->institucion_id;
		$comentario->control_id = Input::get('control_id');
		if(Input::hasFile('archivo')){
			$comentario->observaciones_institucion = null;
			$comentario->cumple = 'si';
			foreach(Input::file('archivo') as $file){
				//generar carpetas con la estructura institucion/control/archivo
				//cambiar el nombre del archivo
				$file->move('public/uploads/1/'.$control->id,$file->getClientOriginalName());
				$archivo = new Archivo;
				$archivo->institucion_id=1;
				$archivo->control_id=Input::get('control_id');
				$archivo->filename=$file->getClientOriginalName();
				$archivo->save();
			}
		}
		$comentario->save();
		return Response::json(['success' => true,'message'=>'<div class="alert alert-success"><strong>Success!</strong> OK.</div>']);
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
						$comentario->anio_implementacion = $objWorksheet->getCellByColumnAndRow(4, $fila)->getValue();
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
}