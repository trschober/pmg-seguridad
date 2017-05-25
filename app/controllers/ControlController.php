<?php

class ControlController extends BaseController{


	public function getIndex(){
		$controles = Control::with(array('comentarios' => function( $query ){
		    $query->where('institucion_id',1);
		}))->paginate(10);
		$this->layout->title= "Revisión de controles";
        $this->layout->content = View::make('controles/listado', array('controles'=>$controles));
	}

	public function getEstado(){
		if(Request::ajax()){
			$control_id = Input::get('control_id');
			$comentario = Comentario::where('institucion_id',1)->where('control_id',$control_id)->first();
			if($comentario===null){
				return Response::json(array(
					'success'=>false,
				    'message'=>'No existe'
				));
			}else{
				return Response::json(array(
					'success'=>true,
				    'message'=>'existe'
				));
			}
		}
	}

	public function setIncumplimiento(){
		/*
		$incumplimiento = Input::get('comentario_incumplimiento');
		$control_id = Input::get('control_id');
		$file = Input::file('archivo');
		//$mime = $archivo->getMimeType();
		if ($file !== null) {
		    echo $file->getClientOriginalExtension();  
		}
		*/
		//print_r(Input::all());

		//echo $incumplimiento."<br>";
		//echo $control_id."<br>";
		//exit;

		//$file = Input::file('archivo');
		//$file->move('public/uploads',$file->getClientOriginalName());
		//return Response::json(['success' => true, 'message'=>'file uploaded']);

		
		foreach(Input::file('archivo') as $file){
			$file->move('public/uploads',$file->getClientOriginalName());
			$archivo = new Archivo;
			$archivo->institucion_id=1;
			$archivo->control_id=Input::get('control_id');
			$archivo->filename=$file->getClientOriginalName();
			$archivo->save();
		}
		
		return Response::json(['success' => true,'message'=>'<div class="alert alert-success"><strong>Success!</strong> OK.</div>']);
		//return Response::json(array('success'=>true,'message'=>'OK'));
	}

}