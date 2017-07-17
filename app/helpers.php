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

	public static function getNumerador($red_expertos = false){
		$valor_institucion = $red_expertos ? Session::get('sesion_institucion') : Auth::user()->institucion_id;
		$controles = Control::with(array('comentarios' => function($query) use($valor_institucion){
				    $query->where('institucion_id',$valor_institucion);
		}))->get();
		$cumplidos=0;
		foreach ($controles as $control) {
			if(count($control->comentarios)>0){
                if($control->comentarios[0]->cumple=='si'){
                	$files = Archivo::where('institucion_id',$valor_institucion)->where('control_id',$control->id)->count();
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