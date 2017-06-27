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
		$numerador = \Helpers::getNumerador();

		//envio de correo cierre proceso para usuarios de perfil ingreso y validador
		$usuarios = Usuario::participantes()->where('institucion_id',Auth::user()->institucion_id)->get();
		foreach ($usuarios as $usuario) {
			$email = $usuario->correo;
			$data = array('usuario' => $usuario);
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				\Mail::send('emails/cierre_proceso', $data, function($message) use ($email) {
			    	$message->subject('Aprobación SSI');
			    	$message->to($email);
				});
			}
		}
		return Redirect::to('bienvenida');
	}

	protected function generarReporte(){
		$objPHPExcel = new PHPExcel();
		
		/* Hoja1: Resumen Institución */	
		$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A1', 'Ministerio')
			            ->setCellValue('A2', 'Institución')
			            ->setCellValue('A3', 'Numerador')
			            ->setCellValue('A4', 'Denominador')
			            ->setCellValue('A5', 'Tramites nivel 2')
			            ->setCellValue('A6', 'Tramites nivel 3')
			            ->setCellValue('A7', 'Tramites nivel 3 (máximo nivel de digitalización)')
			            ->setCellValue('A8', 'Tramites nivel 4 (trámites digitalizados)')
			            ->setCellValue('A9', 'Cantidad de trámites')
			            ->setCellValue('A10', 'Porcentaje');
		$catastro = DB::table('catastros')->where('institucion',$institucion)->first();
		/*
		$cantidad_tramites = DB::table('catastros')->where('institucion',$institucion)->count();
		$cantidad_nivel_0 = DB::table('catastros')->where('edit_nivel_digitalizacion',0)->where('institucion',$institucion)->count();
		$cantidad_nivel_1 = DB::table('catastros')->where('edit_nivel_digitalizacion',1)->where('institucion',$institucion)->count();
        $cantidad_nivel_2 = DB::table('catastros')->where('edit_nivel_digitalizacion',2)->where('institucion',$institucion)->count();
        $cantidad_nivel_3 = DB::table('catastros')->where('edit_nivel_digitalizacion',3)->where('maximo_nivel',0)->where('institucion',$institucion)->count();
        $cantidad_nivel_3_max = DB::table('catastros')->where('edit_nivel_digitalizacion',3)->where('maximo_nivel',1)->where('institucion',$institucion)->count();
        $cantidad_nivel_4 = DB::table('catastros')->where('edit_nivel_digitalizacion',4)->where('institucion',$institucion)->count();
        */
        $numerador = $cantidad_nivel_4 + $cantidad_nivel_3_max;
		$porcentaje = round(($numerador * 100) / $cantidad_tramites,2) .'%';

        $objPHPExcel->getActiveSheet()->setCellValue("B1",$catastro->ministerio);
		$objPHPExcel->getActiveSheet()->setCellValue("B2",$institucion);
		$objPHPExcel->getActiveSheet()->setCellValue("B3",$cantidad_nivel_0);
		$objPHPExcel->getActiveSheet()->setCellValue("B4",$cantidad_nivel_1);
		$objPHPExcel->getActiveSheet()->setCellValue("B5",$cantidad_nivel_2);
		$objPHPExcel->getActiveSheet()->setCellValue("B6",$cantidad_nivel_3);
		$objPHPExcel->getActiveSheet()->setCellValue("B7",$cantidad_nivel_3_max);
		$objPHPExcel->getActiveSheet()->setCellValue("B8",$cantidad_nivel_4);
		$objPHPExcel->getActiveSheet()->setCellValue("B9",$cantidad_tramites);
		$objPHPExcel->getActiveSheet()->setCellValue("B10",$porcentaje);
		$objPHPExcel->getActiveSheet()->setTitle("Presentación");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja1 */

		/* Hoja2: Listado de trámites */
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(1)
		            ->setCellValue('A1', 'Ministerio')
		            ->setCellValue('B1', 'Servicio')
		            ->setCellValue('C1', 'Trámite')
		            ->setCellValue('D1', 'Nivel Digitalización')
		            ->setCellValue('E1', 'URL')
		            ->setCellValue('F1', 'Descripción')
		            ->setCellValue('G1', 'Observación servicio')
		            ->setCellValue('H1', 'Cantidad capturas');

		$rowNumber = 2;
		$catastros = DB::table('catastros')->where('institucion',$institucion)->get();
		foreach ($catastros as $c){
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowNumber,$c->ministerio);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowNumber,$c->institucion);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowNumber,$c->nombre);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowNumber,$c->edit_nivel_digitalizacion);
			if(is_null($c->edit_url) or $c->edit_url == "" ){
				$objPHPExcel->getActiveSheet()->setCellValue("E".$rowNumber,$c->url);
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue("E".$rowNumber,$c->edit_url);
			}
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowNumber,$c->edit_descripcion);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowNumber,$c->observacion_validacion);
			$cantidad_capturas = DB::table('capturas')->where('catastro_id',$c->id_catastro)->count();
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowNumber,$cantidad_capturas);
			$rowNumber++;
		}
		$objPHPExcel->getActiveSheet()->setTitle("Trámites");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja2 */

		/* Guardar excel en disco */
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('uploads/validacion_pmg-'.$institucion_id.'.xls');
		$nombre_archivo = 'uploads/validacion_pmg-'.$institucion_id.'.xls';
	}

}
