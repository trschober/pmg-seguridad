<?php

class Helpers{
	public static function cleanFileName($cadena){
		$cadena = str_replace(" ","-",$cadena);
	    $cadena = preg_replace('/[^a-zA-Z0-9-_.]/','',$cadena);
	    $cadena = strtolower($cadena);
		return $cadena;
	}
}