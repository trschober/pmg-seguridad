<?php

class HomeController extends BaseController {

	public function index(){
		$this->layout->title= "Seguridad de la Información";
    	$this->layout->content = View::make('inicio');
	}

	public function bienvenida(){
		$perfiles_bienvenida = array('ingreso','validador');
		$fechas = Configuracion::fechas()->get();
		$fecha_termino = date('Y/m/d', strtotime($fechas[1]->valor));
		$fecha_inicio  = Carbon::createFromFormat('d-m-Y H:i:s', $fechas[0]->valor .' 00:00:00')->toDateTimeString();
		$data['fecha_termino'] = $fecha_termino;
		$data['total_controles'] = Control::all()->count();
		$data['controles_actualizados'] = Comentario::actualizados()->where('institucion_id',Auth::user()->institucion_id)->count();
		$porcentaje_actualizados = ($data['controles_actualizados']*100)/$data['total_controles'];
		$data['porcentaje_actualizados'] = number_format($porcentaje_actualizados, 1, '.', '');
		$data['habilitado'] = $this->getHabilitacion();
		$data['documentos_descarga'] = Session::get("historial_id")>1 ? true : false;
		$this->layout->title= "Seguridad de la Información";
    	$this->layout->content = View::make('bienvenida',$data);
	}	
}
