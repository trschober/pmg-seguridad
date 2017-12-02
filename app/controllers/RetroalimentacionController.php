<?php

class RetroalimentacionController extends BaseController {
	
	public function index(){
		if(Input::has('institucion'))
			$valor_institucion = Input::get('institucion'); //experto al cambiar de institución
		else
			$valor_institucion = Session::has('sesion_institucion') ? Session::get('sesion_institucion') : Auth::user()->institucion_id; //experto con sesión o usuario de perfil reporte o validador

		if(Auth::user()->perfil==='experto'){
			$instituciones = Institucion::orderBy('servicio')->get();
			Session::put('sesion_institucion',$valor_institucion);
			$data['instituciones']=$instituciones;
		}
		$data['institucion'] = Session::has('activo') ? Institucion::find($valor_institucion) : InstitucionHistorial::where('id',$valor_institucion)->where('historial_id',Session::get('historial_id'))->first();
		$this->layout->title="Retroalimentación";
        $this->layout->content = Session::has('activo') ? View::make('retroalimentacion/inicio',$data) : View::make('retroalimentacion/historial',$data);
	}

	public function setObservacionesRed(){
		$institucion = Institucion::find(Session::get('sesion_institucion'));
		$institucion->observaciones_red = Input::get('observacion_red');
		$institucion->save();

		//Historial
		$institucion = InstitucionHistorial::where('institucion_id',Session::get('sesion_institucion'))->where('historial_id',Session::get('historial_id'))->first();
		if($institucion===null){
			$institucion = new InstitucionHistorial;
			$institucion->institucion_id = Session::get('sesion_institucion');
			$institucion->historial_id = Session::get('historial_id');
		}
		$institucion->observaciones_red = Input::get('observacion_red');
		$institucion->save();

		Session::flash('success', 'Observaciones han sido actualizadas');
		return Redirect::to('retroalimentacion');
	}

	public function setReporteRed(){
		$institucion = Institucion::find(Session::get('sesion_institucion'));
		$objPHPExcel = new PHPExcel();
		
		/* Hoja1: Resumen servicio */	
		$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A1', 'Numerador')
			            ->setCellValue('A2', 'Denominador')
			            ->setCellValue('A3', 'Porcentaje')
			            ->setCellValue('A5', 'Observación General');
        $denominador = \Helpers::getDenominador();
        $numerador = \Helpers::getNumerador(true);
		$porcentaje = round(($numerador * 100) / $denominador,2) .'%';
		$objPHPExcel->getActiveSheet()->setCellValue("B1","N° de controles de seguridad de la Norma NCh-ISO 27001 implementados al año t");
		$objPHPExcel->getActiveSheet()->setCellValue("B2","N° de controles  establecidos en la Norma NCh-ISO 27001");
		$objPHPExcel->getActiveSheet()->setCellValue("B5",$institucion->observaciones_red);
        $objPHPExcel->getActiveSheet()->setCellValue("C1",$numerador);
		$objPHPExcel->getActiveSheet()->setCellValue("C2",$denominador);
		$objPHPExcel->getActiveSheet()->setCellValue("C3",$porcentaje);
		$objPHPExcel->getActiveSheet()->setCellValue("C5",$institucion->observaciones_red);
		$objPHPExcel->getActiveSheet()->setTitle("Resumen");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja1 */

		/* Hoja2: Listado de controles */
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(1)
		            ->setCellValue('A1', 'Código')
		            ->setCellValue('B1', 'Nombre')
		            ->setCellValue('C1', 'Año de formulación')
		            ->setCellValue('D1', 'Implementado')
		            ->setCellValue('E1', 'Año de implementación')
		            ->setCellValue('F1', 'Justificación')
		            ->setCellValue('G1', 'Observaciones de la Red');
		$rowNumber = 2;
		$controles = Control::with(array('comentarios' => function($query){
				    $query->where('institucion_id',Session::get('sesion_institucion'));
			}))->get();

		foreach ($controles as $control){
			if(count($control->comentarios)>0){
				$objPHPExcel->getActiveSheet()->setCellValue("A".$rowNumber,$control->codigo);
				$objPHPExcel->getActiveSheet()->setCellValue("B".$rowNumber,$control->nombre);
				$objPHPExcel->getActiveSheet()->setCellValue("C".$rowNumber,$control->comentarios[0]->anio_compromiso);
				$objPHPExcel->getActiveSheet()->setCellValue("D".$rowNumber,$control->comentarios[0]->cumple);
				$objPHPExcel->getActiveSheet()->setCellValue("E".$rowNumber,$control->comentarios[0]->anio_implementacion);
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowNumber,$control->comentarios[0]->observaciones_institucion);
				$objPHPExcel->getActiveSheet()->setCellValue("G".$rowNumber,$control->comentarios[0]->observaciones_red);
				$rowNumber++;
			}
		}
		$objPHPExcel->getActiveSheet()->setTitle("Controles");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja2 */

		/* Hoja3: Análisis de riesgos */
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(2)
		            ->setCellValue('A1', 'Archivos adjuntos');
		$rowNumber = 2;
		$riesgos = Riesgo::where('institucion_id',Session::get('sesion_institucion'))->get();
		$tiene_riesgos = count($riesgos) ? 'si' : 'no';
		$objPHPExcel->getActiveSheet()->setCellValue("B1",$tiene_riesgos);
		foreach ($riesgos as $riesgo){
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowNumber,$riesgo->filename);
			$rowNumber++;
		}
		$objPHPExcel->getActiveSheet()->setTitle("Análisis de Riesgo");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja3 */



		/* Guardar excel en disco */
		$nombre_archivo = 'uploads/reportes/reporte-red-'.Session::get('sesion_institucion').'.xls';
		$carpeta_reportes = 'uploads/reportes';
		if(!is_dir($carpeta_reportes))
			mkdir($carpeta_reportes);
	    

		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($nombre_archivo);
		Session::flash('success', 'Reporte generado exitósamente');
		return Redirect::to('retroalimentacion');
	}

	public function getReporteRed(){
		return Response::download('uploads/reportes/reporte-red-'.Auth::user()->institucion_id.'.xls');
	}
}