<?php

class Helpers{
	public static function cleanFileName($cadena){
		$cadena = str_replace(" ","-",$cadena);
	    $cadena = preg_replace('/[^a-zA-Z0-9-_.]/','',$cadena);
	    $cadena = strtolower($cadena);
		return $cadena;
	}

	public static function getDenominador(){
		$controles = Control::all();
		return count($controles);
	}

	public static function getNumerador(){
		$controles = Control::with(array('comentarios' => function($query){
				    $query->where('institucion_id',Auth::user()->institucion_id);
		}))->get();
		$cumplidos=0;
		foreach ($controles as $control) {
			if(count($control->comentarios)>0){
                if($control->comentarios[0]->cumple=='si'){
                	$files = Archivo::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control->id)->count();
                	if($files>0)
                		$cumplidos++;
                }
            }
		}
		return $cumplidos;
	}

	public static function getListadoInstituciones(){
		return Institucion::orderBy('servicio')->get();
	}
}