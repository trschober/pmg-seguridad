<?php
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

class GestionController extends BaseController {

	public function getInstituciones(){
		$this->layout->title="Instituciones";
		$data['total_controles'] = Control::all()->count();
		$data['instituciones'] = DB::table('comentarios')
			->select(DB::raw('instituciones.id as id,
									  instituciones.servicio as servicio,
									  instituciones.estado as estado,
	        						  sum(case when cumple="si" or cumple="no" then 1 else 0 end) as cumple,
	        						  sum(case when cumple="si" then 1 else 0 end) as implementado,
	        						  sum(case when cumple="no" then 1 else 0 end) as no_implementado,
	        						  (select count(riesgos.id) from riesgos where riesgos.institucion_id = instituciones.id) as cantidad_archivos_riesgo'))
            ->join('instituciones','instituciones.id','=','comentarios.institucion_id')
            ->groupBy('instituciones.id')
            ->get();
        $this->layout->content = View::make('gestion/instituciones',$data);
	}

	public function updateInstitucion(){
		if(Auth::user()->perfil==='experto'){
			$institucion = Institucion::find(Input::get('institucion_id'));
			$institucion->estado = Input::get('estado');
			$institucion->save();
			return Response::json(['success' => true]);
		}
		return Response::json(['success' => false]);
	}

	public function getUsuarios(){
		if(Input::has('institucion'))
			$valor_institucion = Input::get('institucion'); //experto al cambiar de institución
		else
			$valor_institucion = Session::has('sesion_institucion') ? Session::get('sesion_institucion') : Auth::user()->institucion_id; //experto con sesión o usuario de perfil reporte o validador

		$data['usuarios'] = Usuario::with('institucion')->where('institucion_id',$valor_institucion)->get();
		$data['instituciones'] = \Helpers::getListadoInstituciones();
		$this->layout->title="Usuarios";
        $this->layout->content = View::make('gestion/listado_usuarios',$data);
	}

	public function getUsuarioDetalle($usuario_id = NULL){
		$usuario = Usuario::where('id',$usuario_id)->first();
		$data['usuario'] = $usuario!=null ? $usuario : new Usuario();
		$data['instituciones'] = \Helpers::getListadoInstituciones();
		$this->layout->title="Usuarios";
        $this->layout->content = View::make('gestion/editar_usuario',$data);
	}

	public function updateUsuario(){
		$reglas =  array(
            'rut'  => 'required','min:8',
            'nombres' => 'required',
            'apellidos' => 'required',
            'correo' => 'required|email',
            'perfil' => 'required',
            'institucion_usuario' => 'required'
        );
        $respuesta = Validator::make(Input::all(),$reglas);
        if ($respuesta->fails()) {
			return Redirect::to('gestion/usuarios/editar')->withErrors($respuesta)->withInput();
		}else{
			$rut = str_replace(".", "",trim(Input::get('rut')));
			$usuario = Usuario::where('rut',$rut)->first();
			if($usuario===null)
				$usuario = new Usuario();

    	 	$usuario->rut = $rut;
    	 	$usuario->nombres = Input::get('nombres');
    	 	$usuario->apellidos = Input::get('apellidos');
    	 	$usuario->correo = Input::get('correo');
    	 	$usuario->perfil = Input::get('perfil');
    	 	$usuario->institucion_id = Input::get('institucion_usuario');
    	 	$usuario->save();
    	 	Session::flash('usuario_ok','Usuario generado exitósamente');
    	 	return Redirect::to('gestion/usuarios');
		}
	}

	public function deleteUsuario($usuario_id = NULL){
		if(Auth::user()->perfil==='experto'){
			$usuario = Usuario::where('id',$usuario_id)->first();
			if($usuario!=null){
				$usuario->delete();
			}
			return Redirect::to('gestion/usuarios');
		}else{
			return Redirect::to('bienvenida');
		}
	}

	public function institucionesExportar(){
		$objPHPExcel = new PHPExcel();
		$total_controles = Control::all()->count();
		$listado = DB::table('comentarios')
			->select(DB::raw('instituciones.servicio as servicio,
									  instituciones.estado as estado,
	        						  sum(case when cumple="si" or cumple="no" then 1 else 0 end) as cumple,
	        						  sum(case when cumple="si" then 1 else 0 end) as implementado,
	        						  sum(case when cumple="no" then 1 else 0 end) as no_implementado,
	        						  (select count(riesgos.id) from riesgos where riesgos.institucion_id = instituciones.id) as cantidad_archivos_riesgo'))
            ->join('instituciones','instituciones.id','=','comentarios.institucion_id')
            ->groupBy('instituciones.id')
            ->get();
        $rowNumber = 2;
		$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A1', 'Servicio')
			            ->setCellValue('B1', 'Estado')
			            ->setCellValue('C1', 'Controles actualizados')
			            ->setCellValue('D1', 'Porcentaje actualizados')
			            ->setCellValue('E1', 'Implementado')
			            ->setCellValue('F1', 'No implementado')
			            ->setCellValue('G1', 'Análisis de riesgo');
		foreach ($listado as $institucion){
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowNumber,$institucion->servicio);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowNumber,$institucion->estado);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowNumber,$institucion->cumple);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowNumber,number_format(($institucion->cumple*100)/$total_controles,1, '.','').'%');
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowNumber,$institucion->implementado);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowNumber,$institucion->no_implementado);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowNumber,$institucion->cantidad_archivos_riesgo==0? 'No' : 'Si');
			$rowNumber++;
		}

		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		$hoy = date("Y-m-d H:i:s");
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="instituciones-ssi-'.date("d-m-Y",strtotime($hoy)).'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		//header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	public function detalleInstitucionesExportar(){
		$objPHPExcel = new PHPExcel();
		$listado = DB::table('comentarios')
			->select(DB::raw('  instituciones.id as institucion_id,
								instituciones.ministerio as ministerio,
								instituciones.servicio as servicio,
								instituciones.codigo_indicador as codigo_indicador,
								instituciones.codigo_servicio as codigo_servicio,
	        						  controles.id as control_id,
	        						  controles.codigo as control_codigo,
	        						  controles.nombre as control_nombre,
	        						  comentarios.cumple as cumple,
	        						  comentarios.desc_medio_verificacion as desc_medio_verificacion,
	        						  comentarios.observaciones_institucion as observaciones_institucion'))
            ->join('instituciones','instituciones.id','=','comentarios.institucion_id')
            ->join('controles','controles.id','=','comentarios.control_id')
            ->orderBy('instituciones.id','ASC')
            ->orderBy('controles.id','ASC')
            ->get();
        $rowNumber = 2;
		$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A1', 'Ministerio')
			            ->setCellValue('B1', 'Servicio')
			            ->setCellValue('C1', 'Código indicador')
			            ->setCellValue('D1', 'id_servicio')
			            ->setCellValue('E1', 'control_seguridad_informacion_tbid')
			            ->setCellValue('F1', 'Control')
			            ->setCellValue('G1', 'Control Descripción')
			            ->setCellValue('H1', 'Implementado al 2017')
			            ->setCellValue('I1', 'Razones control no implementado')
			            ->setCellValue('J1', 'Nombre Medios de Verificación')
			            ->setCellValue('K1', 'Descripción Medios de Verificación');
		foreach ($listado as $reg){
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowNumber,$reg->ministerio);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowNumber,$reg->servicio);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowNumber,$reg->codigo_indicador);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowNumber,$reg->codigo_servicio);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowNumber,$reg->control_id);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowNumber,$reg->control_codigo);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowNumber,$reg->control_nombre);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowNumber,$reg->cumple);
			$objPHPExcel->getActiveSheet()->setCellValue("I".$rowNumber,$reg->observaciones_institucion);
			$archivos = Archivo::where('institucion_id',$reg->institucion_id)->where('control_id',$reg->control_id)->get();
			$listado_archivos = "";
			foreach($archivos as $archivo){
				$listado_archivos .= $archivo->filename."\n";
			}
			$objPHPExcel->getActiveSheet()->setCellValue("J".$rowNumber,$listado_archivos);
			$objPHPExcel->getActiveSheet()->setCellValue("K".$rowNumber,$reg->desc_medio_verificacion);
			$rowNumber++;
		}
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		$hoy = date("Y-m-d H:i:s");
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="detalle-instituciones-'.date("d-m-Y",strtotime($hoy)).'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		//header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	public function exportarInformes(){
		if(file_exists('uploads/reportes/informes.zip')){
			unlink('uploads/reportes/informes.zip');
		}
		$instituciones = Institucion::all();
		$archivos = array();
		foreach ($instituciones as $ins) {
			if(file_exists('uploads/reportes/reporte-red-'.$ins->id.'.xls')){
				array_push($archivos,'uploads/reportes/reporte-red-'.$ins->id.'.xls');
			}
		}
		$result = \Helpers::create_zip($archivos,'uploads/reportes/informes.zip');
		return Response::download('uploads/reportes/informes.zip');
	}

	public function exportarCertificados(){
		if(file_exists('uploads/cierre/certificados.zip')){
			unlink('uploads/cierre/certificados.zip');
		}
		$instituciones = Institucion::all();
		$archivos = array();
		foreach ($instituciones as $ins) {
			if(file_exists('uploads/cierre/certificado-cierre-'.$ins->id.'.pdf')){
				array_push($archivos,'uploads/cierre/certificado-cierre-'.$ins->id.'.pdf');
			}
		}
		$result = \Helpers::create_zip($archivos,'uploads/reportes/certificados.zip');
		return Response::download('uploads/reportes/certificados.zip');
	}

	public function exportarCumplimientos(){
		if(file_exists('uploads/reportes/cumplimientos.zip')){
			unlink('uploads/reportes/cumplimientos.zip');
		}
		$instituciones = Institucion::all();
		$archivos = array();
		foreach ($instituciones as $ins) {
			if(file_exists('uploads/cierre/informe-cumplimiento-'.$ins->id.'.xls')){
				array_push($archivos,'uploads/cierre/informe-cumplimiento-'.$ins->id.'.xls');
			}
		}
		$result = \Helpers::create_zip($archivos,'uploads/reportes/cumplimientos.zip');
		return Response::download('uploads/reportes/cumplimientos.zip');
	}

	public function fixFiles($institucion_id = NULL){
		if(Auth::user()->perfil==='experto'){
			$institucion = Institucion::find($institucion_id);
			$archivos = Archivo::where('institucion_id',$institucion_id)->whereNull('deleted_at')->get();
			foreach($archivos as $archivo){
				$filename_old = $archivo->filename;
				$pos = strpos($archivo->filename, "_A");
				$nombre_archivo_original = substr($archivo->filename,$pos,500);
				$nombre_archivo_nuevo = $institucion->codigo_indicador.'_'.$institucion->codigo_servicio.'_'.$archivo->control_id.'_'.$institucion->sigla.$nombre_archivo_original;
				$archivo->filename = $nombre_archivo_nuevo;
				$archivo->save();
				if(file_exists('uploads/controles/'.$institucion_id.'/'.$archivo->control_id.'/'.$filename_old))
					rename('uploads/controles/'.$institucion_id.'/'.$archivo->control_id.'/'.$filename_old,'uploads/controles/'.$institucion_id.'/'.$archivo->control_id.'/'.$archivo->filename);
			}
			$archivos = ArchivoHistorial::where('institucion_id',$institucion_id)->where('historial_id',2)->get();
			foreach($archivos as $archivo){
				$filename_old = $archivo->filename;
				$pos = strpos($archivo->filename, "_A");
				$nombre_archivo_original = substr($archivo->filename,$pos,500);
				$nombre_archivo_nuevo = $institucion->codigo_indicador.'_'.$institucion->codigo_servicio.'_'.$archivo->control_id.'_'.$institucion->sigla.$nombre_archivo_original;
				$archivo->filename = $nombre_archivo_nuevo;
				$archivo->save();
			}
		}else{
			return Redirect::to('bienvenida');
		}
	}
}