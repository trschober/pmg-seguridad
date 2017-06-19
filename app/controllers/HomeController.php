<?php

class HomeController extends BaseController {

	public function index(){
		$this->layout->title= "Seguridad de la Información";
    	$this->layout->content = View::make('inicio');
	}

	public function bienvenida(){
		$fechas = Configuracion::fechas()->get();
		$fecha_termino = date('Y-m-d', strtotime($fechas[1]->valor));
		$fecha_inicio  = Carbon::createFromFormat('d-m-Y H:i:s', $fechas[0]->valor .' 00:00:00')->toDateTimeString();
		//$fecha_termino = Carbon::createFromFormat('d-m-Y H:i:s', $fecha_termino.' 00:00:00')->toDateTimeString();
		//$fecha_termino_cronometro = Carbon::createFromFormat('Y-m-d', $fecha_termino)->toDateTimeString();
		//$fecha_inicio = Carbon::parse($fechas[0]->valor);
		//$now = Carbon::now()->toDateString();
		$now = Carbon::now();
		$data['fecha_termino'] = $fecha_termino;
		$data['total_controles'] = Control::all()->count();
		$data['controles_actualizados'] = Comentario::actualizados()->where('institucion_id',Auth::user()->institucion_id)->count();
		$porcentaje_actualizados = ($data['controles_actualizados']*100)/$data['total_controles'];
		$data['porcentaje_actualizados'] = number_format($porcentaje_actualizados, 1, '.', '');
		
		$this->layout->title= "Seguridad de la Información";
    	$this->layout->content = View::make('bienvenida',$data);
	}

}
