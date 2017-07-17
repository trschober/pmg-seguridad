<?php

class InstitucionController extends BaseController {

	public function setAprobacion(){
		$controles_actualizados = Comentario::actualizados()->where('institucion_id',Auth::user()->institucion_id)->count();
		$total_controles = Control::all()->count();
		$total_paginas = ceil($total_controles/10);
		$controles = Control::with(array('comentarios' => function($query){
		    $query->where('institucion_id',Auth::user()->institucion_id);
		}))->count();
		if($controles_actualizados<$total_controles){
			Session::put('marca','necesario actualizar');
			for($i=1;$i<=$total_paginas;$i++){
				Paginator::setCurrentPage($i);
				$controles = Control::with(array('comentarios' => function($query){
				    $query->where('institucion_id',Auth::user()->institucion_id);
				}))->paginate(10);
				$faltantes=0;
				foreach ($controles as $control) {
					if(count($control->comentarios)==0){
		                $faltantes++;
		            }else{
		                if(is_null($control->comentarios[0]->cumple))
		                    $faltantes++;
		                elseif($control->comentarios[0]->cumple=='si'){
		                	$files = Archivo::where('institucion_id',Auth::user()->institucion_id)->where('control_id',$control->id)->count();
		                	if($files==0)
		                		$faltantes++;
		                }
		            }
				}
				if($faltantes>0){
					$url = URL::to('controles?page='.$i);
		    		return Redirect::to($url);
				}
			}
		}else{
			//enviar a validador
			$institucion = Institucion::find(Auth::user()->institucion_id);
			$institucion->estado = 'enviado';
			$institucion->save();
			//envio de correo a usuarios de perfil ingreso
			$usuarios = Usuario::where('institucion_id',Auth::user()->institucion_id)->where('perfil','ingreso')->get();
			foreach ($usuarios as $usuario) {
				$email = $usuario->correo;
				$data = array('usuario' => $usuario);
				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					\Mail::send('emails/enviado_validacion_ingreso', $data, function($message) use ($email) {
				    	$message->subject('Enviado a validación SSI');
				    	$message->to($email);
					});
				}
			}
			//envio de correo perfil validador
			$usuarios = Usuario::where('institucion_id',Auth::user()->institucion_id)->where('perfil','validador')->get();
			foreach ($usuarios as $usuario) {
				$email = $usuario->correo;
				$data = array('usuario' => $usuario);
				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					\Mail::send('emails/enviado_validacion_validador', $data, function($message) use ($email) {
				    	$message->subject('Enviado a validación SSI');
				    	$message->to($email);
					});
				}
			}
			return Redirect::to('bienvenida');
		}
	}

	public function setRechazo(){
		$institucion = Institucion::find(Auth::user()->institucion_id);
		$institucion->estado = 'rechazado';
		$institucion->observaciones_aprobador = Input::has('observaciones') ? Input::get('observaciones') : NULL;
		$institucion->save();
		//envio de correo rechazo a usuarios de perfil ingreso
		$usuarios = Usuario::where('institucion_id',Auth::user()->institucion_id)->where('perfil','ingreso')->get();
		foreach ($usuarios as $usuario) {
			$email = $usuario->correo;
			$data = array('usuario' => $usuario);
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				\Mail::send('emails/rechazo_validador', $data, function($message) use ($email) {
			    	$message->subject('Rechazo SSI');
			    	$message->to($email);
				});
			}
		}
		return Redirect::to('bienvenida');
	}

	public function setCierre(){
		$institucion = Institucion::find(Auth::user()->institucion_id);
		$institucion->estado = 'cerrado';
		$institucion->save();
		$nombre_archivo = $this->reporteCierre();

		//envio de correo cierre proceso para usuarios de perfil ingreso y validador
		$usuarios = Usuario::participantes()->where('institucion_id',Auth::user()->institucion_id)->get();
		foreach ($usuarios as $usuario) {
			$email = $usuario->correo;
			$data = array('usuario' => $usuario);
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				\Mail::send('emails/cierre_proceso', $data, function($message) use ($nombre_archivo,$email) {
			    	$message->subject('Aprobación SSI');
			    	$message->attach($nombre_archivo);
			    	$message->to($email);
				});
			}
		}
		return Redirect::to('bienvenida');
	}

	protected function reporteCierre(){
		$objPHPExcel = new PHPExcel();
		
		/* Hoja1: Resumen servicio */	
		$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A1', 'Ministerio')
			            ->setCellValue('A2', 'Servicio')
			            ->setCellValue('A3', 'Numerador')
			            ->setCellValue('A4', 'Denominador')
			            ->setCellValue('A5', 'Porcentaje');
        $denominador = \Helpers::getDenominador();
        $numerador = \Helpers::getNumerador();
		$porcentaje = round(($numerador * 100) / $denominador,2) .'%';
        $objPHPExcel->getActiveSheet()->setCellValue("B1",Auth::user()->institucion->ministerio);
		$objPHPExcel->getActiveSheet()->setCellValue("B2",Auth::user()->institucion->servicio);
		$objPHPExcel->getActiveSheet()->setCellValue("B3",$numerador);
		$objPHPExcel->getActiveSheet()->setCellValue("B4",$denominador);
		$objPHPExcel->getActiveSheet()->setCellValue("B5",$porcentaje);
		$objPHPExcel->getActiveSheet()->setTitle("Resumen");
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
		            ->setCellValue('G1', 'Justificación');
		$rowNumber = 2;
		$controles = Control::with(array('comentarios' => function($query){
				    $query->where('institucion_id',Auth::user()->institucion_id);
			}))->get();
		foreach ($controles as $control){
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowNumber,$control->id);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowNumber,$control->codigo);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowNumber,$control->nombre);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowNumber,$control->comentarios[0]->anio_compromiso);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowNumber,$control->comentarios[0]->cumple);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowNumber,$control->comentarios[0]->anio_implementacion);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowNumber,$control->comentarios[0]->observaciones_institucion);
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
		$riesgos = Riesgo::where('institucion_id',Auth::user()->institucion_id)->get();
		foreach ($riesgos as $riesgo){
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowNumber,$riesgo->filename);
			$rowNumber++;
		}
		$objPHPExcel->getActiveSheet()->setTitle("Análisis de Riesgo");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja3 */

		/* Guardar excel en disco */
		$nombre_archivo = 'public/uploads/reportes/ssi-reporte-'.Auth::user()->institucion_id.'.xls';
		$carpeta_reportes = 'public/uploads/reportes';
		if(!is_dir($carpeta_reportes))
			mkdir($carpeta_reportes);
	    	
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($nombre_archivo);
		return $nombre_archivo;
	}

	public function getReporteCierre(){
		return Response::download('public/uploads/reportes/ssi-reporte-'.Auth::user()->institucion_id.'.xls');
	}
}
