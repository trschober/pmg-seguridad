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

	public static function create_zip($files = array(),$destination = '',$overwrite = false) {
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination) && !$overwrite) { return false; }
		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($file,$file);
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			
			//close the zip -- done!
			$zip->close();
			
			//check to make sure the file exists
			return file_exists($destination);
		}
		else
		{
			return false;
		}
	}

}