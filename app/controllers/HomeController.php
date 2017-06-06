<?php

class HomeController extends BaseController {

	public function index(){
		$this->layout->title= "Seguridad de la Información";
    	$this->layout->content = View::make('inicio');
	}

	public function bienvenida(){
		$this->layout->title= "Seguridad de la Información";
    	$this->layout->content = View::make('bienvenida');
	}

}
