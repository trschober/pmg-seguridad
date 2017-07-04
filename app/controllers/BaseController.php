<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected $layout = 'template';

	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	//Habilitación segun perfil
	public function getHabilitacion(){
		$habilitado = true;
		$fechas = Configuracion::fechas()->get();
		$fecha_inicio  = Carbon::createFromFormat('d-m-Y H:i:s', $fechas[0]->valor .' 00:00:00')->toDateTimeString();
		$fecha_termino = Carbon::createFromFormat('d-m-Y H:i:s', $fechas[1]->valor .' 23:59:59')->toDateTimeString();
		$hoy = Carbon::today();
		//echo $hoy>=$fecha_inicio && $hoy<=$fecha_termino ? 'ok' : 'nok';
		if(Auth::user()->perfil=='ingreso' && $hoy<$fecha_inicio && $fecha_termino>$hoy){
			$habilitado = false;
		}
		elseif(Auth::user()->perfil=='ingreso' && !in_array(Auth::user()->institucion->estado,array("ingresado","rechazado"))  ){
			$habilitado = false;
		}elseif(in_array(Auth::user()->perfil, array('validador','experto','evaluador'))){
			$habilitado = false;
		}
		return $habilitado;
	}

}
