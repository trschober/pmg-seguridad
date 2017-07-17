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
		$data['institucion'] = Institucion::find($valor_institucion);
		$this->layout->title="Retroalimentación";
        $this->layout->content = View::make('retroalimentacion/inicio',$data);
	}

	public function setObservacionesRed(){
		$institucion = Institucion::find(Session::get('sesion_institucion'));
		$institucion->observaciones_red = Input::get('observacion_red');
		$institucion->save();
		return Redirect::to('retroalimentacion');
	}

	public function setReporteRed(){
		$institucion = Institucion::find(Session::get('sesion_institucion'));
		$objPHPExcel = new PHPExcel();
		
		/* Hoja1: Resumen servicio */	
		$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A1', 'Ministerio')
			            ->setCellValue('A2', 'Servicio')
			            ->setCellValue('A3', 'Numerador')
			            ->setCellValue('A4', 'Denominador')
			            ->setCellValue('A5', 'Porcentaje')
			            ->setCellValue('A6', 'Observación General');
        $denominador = \Helpers::getDenominador();
        $numerador = \Helpers::getNumerador(true);
		$porcentaje = round(($numerador * 100) / $denominador,2) .'%';
        $objPHPExcel->getActiveSheet()->setCellValue("B1",$institucion->ministerio);
		$objPHPExcel->getActiveSheet()->setCellValue("B2",$institucion->servicio);
		$objPHPExcel->getActiveSheet()->setCellValue("B3",$numerador);
		$objPHPExcel->getActiveSheet()->setCellValue("B4",$denominador);
		$objPHPExcel->getActiveSheet()->setCellValue("B5",$porcentaje);
		$objPHPExcel->getActiveSheet()->setCellValue("B6",$institucion->observaciones_red);
		$objPHPExcel->getActiveSheet()->setTitle("Presentación");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja1 */

		/* Hoja2: Listado de controles */
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(1)
		            ->setCellValue('A1', 'ID')
		            ->setCellValue('B1', 'Código')
		            ->setCellValue('C1', 'Nombre')
		            ->setCellValue('D1', 'Año Formulación')
		            ->setCellValue('E1', 'Implementado')
		            ->setCellValue('F1', 'Año Documentación')
		            ->setCellValue('G1', 'Justificación')
		            ->setCellValue('H1', 'Observaciones de la Red');
		$rowNumber = 2;
		$controles = Control::with(array('comentarios' => function($query){
				    $query->where('institucion_id',Session::get('sesion_institucion'));
			}))->get();
		foreach ($controles as $control){
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowNumber,$control->id);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowNumber,$control->codigo);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowNumber,$control->nombre);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowNumber,$control->comentarios[0]->anio_compromiso);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowNumber,$control->comentarios[0]->cumple);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowNumber,$control->comentarios[0]->anio_implementacion);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowNumber,$control->comentarios[0]->observaciones_institucion);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowNumber,$control->comentarios[0]->observaciones_red);
			$rowNumber++;
		}
		$objPHPExcel->getActiveSheet()->setTitle("Controles");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja2 */

		/* Hoja3: Análisis de riesgos */
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(2)
		            ->setCellValue('A1', 'Archivos');
		$rowNumber = 2;
		$riesgos = Riesgo::where('institucion_id',Session::get('sesion_institucion'))->get();
		foreach ($riesgos as $riesgo){
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowNumber,$riesgo->filename);
			$rowNumber++;
		}
		$objPHPExcel->getActiveSheet()->setTitle("Análisis de Riesgo");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja3 */	

		/* Guardar excel en disco */
		$nombre_archivo = 'public/uploads/reportes/reporte-red-'.Session::get('sesion_institucion').'.xls';
		$carpeta_reportes = 'public/uploads/reportes';
		if(!is_dir($carpeta_reportes))
			mkdir($carpeta_reportes);
	    	
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($nombre_archivo);
		return Redirect::to('retroalimentacion');
	}

	public function getReporteRed(){
		return Response::download('public/uploads/reportes/reporte-red-'.Auth::user()->institucion_id.'.xls');
	}
}