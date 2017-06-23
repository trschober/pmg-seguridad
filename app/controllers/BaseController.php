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
		if(Auth::user()->perfil=='reporte' && !in_array(Auth::user()->institucion->estado,array("ingresado","rechazado"))){
			$habilitado = false;
		}elseif(in_array(Auth::user()->perfil, array('aprobador','experto','evaluador'))){
			$habilitado = false;
		}
		return $habilitado;
	}

}
