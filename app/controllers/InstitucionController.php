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
		$informe_cumplimiento = $this->informeCumplimiento();

		//envio de correo cierre proceso para usuarios de perfil ingreso y validador
		$usuarios = Usuario::participantes()->where('institucion_id',Auth::user()->institucion_id)->get();
		foreach ($usuarios as $usuario) {
			$email = $usuario->correo;
			$data = array('usuario' => $usuario);
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				\Mail::send('emails/cierre_proceso', $data, function($message) use ($nombre_archivo,$email,$informe_cumplimiento) {
			    	$message->subject('Aprobación SSI');
			    	$message->attach($nombre_archivo);
			    	$message->attach($informe_cumplimiento);
			    	$message->to($email);
				});
			}
		}
		return Redirect::to('bienvenida');
	}

	public function reporteCierre(){

		$denominador = \Helpers::getDenominador();
        $numerador = \Helpers::getNumerador();
		$porcentaje = round(($numerador * 100) / $denominador,2) .'%';

		/* Creación del PDF */
		$fpdf = new Fpdf();
		$fpdf->AddPage();
		$fpdf->SetFont('Arial','',12);
		// Presentación
		$image1 = public_path().'/img/logo-segpres.png';
		$image2 = public_path().'/img/logo-interior.png';
		$image3 = public_path().'/img/logo-subtel.png';
		$fpdf->SetXY(0, 0);
	    $fpdf->Cell(0,10,$fpdf->Image($image1, 50, 5, 33.78),0,0,'C');
	    $fpdf->SetXY(0, 0);
	    $fpdf->Cell(0,10,$fpdf->Image($image2, 90, 5, 33.78),0,0,'C');
	    $fpdf->SetXY(0, 0);
	    $fpdf->Cell(0,10,$fpdf->Image($image3, 130, 5, 33.78),0,0,'C');
	    $fpdf->SetXY(10, 50);
	    $fpdf->SetFont('Arial','B',12);
	    $fpdf->Cell(0,10,"Certificado Red de Expertos SSI",0,0,'C');
	    $fpdf->SetFont('Arial','',12);
		$fpdf->SetXY(10, 55);
	    $fpdf->Cell(0,10,utf8_decode("Evaluación Indicador SSI 2017"),0,0,'C');
	    $fpdf->SetFont('Arial','',12);
	   	$fpdf->SetXY(10, 70);
	    $fpdf->Cell(0,10,utf8_decode("Con fecha ".date("d-m-Y").", el Servicio ".Auth::user()->institucion->servicio),0,0,'L');
	   	$fpdf->SetXY(10, 75);
	    $fpdf->Cell(0,10,utf8_decode("ha informado a la presente Red de Expertos, a través de la Plataforma dispuesta por SEGPRES"),0,0,'L');
	    $fpdf->SetXY(10, 80);
	    $fpdf->Cell(0,10,utf8_decode("la siguiente medición del Indicador de Seguridad de la Información:"),0,0,'L');
		$fpdf->SetXY(10, 90);
		$fpdf->Cell(0,10,utf8_decode("(Nº de controles de seguridad de la Norma NCh-ISO 27001 implementados"),0,0,'C');
		$fpdf->SetXY(10, 95);
		$fpdf->Cell(0,10,utf8_decode("para mitigar riesgos de seguridad de la información al año 2017/"),0,0,'C');
		$fpdf->SetXY(10, 100);
		$fpdf->Cell(0,10,utf8_decode("N° total de controles establecidos en la Norma NCh-ISO 27001"),0,0,'C');
		$fpdf->SetXY(10, 105);
		$fpdf->Cell(0,10,utf8_decode("para mitigar riesgos de seguridad de la información)*100"),0,0,'C');
		$fpdf->SetXY(10, 115);
		$fpdf->Cell(0,10,utf8_decode("Por medio del presente documento, se certifica que el servicio ha ingresado"),0,0,'L');
		$fpdf->SetXY(10, 120);
		$fpdf->Cell(0,10,utf8_decode("correctamente la información a la Plataforma ssi.digital.gob.cl, en el marco de la"),0,0,'L');
		$fpdf->SetXY(10, 125);
		$fpdf->Cell(0,10,utf8_decode("evaluación del indicador de SSI para el periodo 2017."),0,0,'L');
		$fpdf->SetFont('Arial','B',12);
		$fpdf->SetXY(10, 140);
		$fpdf->Cell(0,10,utf8_decode("Medición del Servicio ".Auth::user()->institucion->servicio." :"),0,0,'L');
		$fpdf->SetFont('Arial','B',12);
		$fpdf->SetXY(10, 155);
		$fpdf->Cell(0,10,utf8_decode("Numerador: ".$numerador),0,0,'L');
		$fpdf->SetXY(10, 165);
		$fpdf->Cell(0,10,utf8_decode("Denominador: ".$denominador),0,0,'L');
		$fpdf->SetXY(10, 175);
		$fpdf->Cell(0,10,utf8_decode("Porcentaje(resultado): ".$porcentaje),0,0,'L');
	    if(!is_dir("uploads/cierre"))
			mkdir("uploads/cierre");
		$nombre_archivo = "uploads/cierre/certificado-cierre-".Auth::user()->institucion->id.".pdf";
		$fpdf->Output($nombre_archivo,'F');
		return $nombre_archivo;
	}

	public function getReporteCierre(){
		return Response::download('uploads/cierre/certificado-cierre-'.Auth::user()->institucion_id.'.pdf');
	}

	public function informeCumplimiento(){
		$denominador = \Helpers::getDenominador();
        $numerador = \Helpers::getNumerador();
		$porcentaje = round(($numerador * 100) / $denominador,2) .'%';
		$objPHPExcel = new PHPExcel();
		
		/* Hoja1: Resumen servicio */	
		$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A4', 'Numerador')
			            ->setCellValue('A5', 'Denominador')
			            ->setCellValue('A6', 'Porcentaje');
		
		$objPHPExcel->getActiveSheet()->setCellValue("A1",Auth::user()->institucion->ministerio);
		$objPHPExcel->getActiveSheet()->setCellValue("A2",Auth::user()->institucion->servicio);
		$objPHPExcel->getActiveSheet()->setCellValue("B4","N° de controles de seguridad de la Norma NCh-ISO 27001 implementados al año t");
		$objPHPExcel->getActiveSheet()->setCellValue("B5","N° de controles  establecidos en la Norma NCh-ISO 27001");
        $objPHPExcel->getActiveSheet()->setCellValue("C4",$numerador);
		$objPHPExcel->getActiveSheet()->setCellValue("C5",$denominador);
		$objPHPExcel->getActiveSheet()->setCellValue("C6",$porcentaje);
		$objPHPExcel->getActiveSheet()->setTitle("Resumen");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja1 */

		/* Hoja2: Listado de controles */
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(1)
		            ->setCellValue('A1', 'Código')
		            ->setCellValue('B1', 'Nombre')
		            ->setCellValue('C1', 'Implementado')
		            ->setCellValue('D1', 'Año primera implementación')
		            ->setCellValue('E1', 'Descripción de medios de Verificación')
		            ->setCellValue('F1', 'Lista Medios de Verificación')
		            ->setCellValue('G1', 'Justificación no implementación');
		$rowNumber = 2;
		$controles = Control::with(array('comentarios' => function($query){
				    $query->where('institucion_id',Auth::user()->institucion->id);
			}))->get();

		foreach ($controles as $control){
			if(count($control->comentarios)>0){
				$objPHPExcel->getActiveSheet()->setCellValue("A".$rowNumber,$control->codigo);
				$objPHPExcel->getActiveSheet()->setCellValue("B".$rowNumber,$control->nombre);
				$objPHPExcel->getActiveSheet()->setCellValue("C".$rowNumber,$control->comentarios[0]->cumple);
				$objPHPExcel->getActiveSheet()->setCellValue("D".$rowNumber,$control->comentarios[0]->anio_implementacion);
				$objPHPExcel->getActiveSheet()->setCellValue("E".$rowNumber,$control->comentarios[0]->desc_medio_verificacion);
				$archivos = Archivo::where('institucion_id',Auth::user()->institucion->id)->where('control_id',$control->id)->get();
				$listado_archivos = "";
				foreach($archivos as $archivo){
					$listado_archivos .= $archivo->filename."\n";
				}
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowNumber,$listado_archivos);
				$objPHPExcel->getActiveSheet()->getStyle("F".$rowNumber)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue("G".$rowNumber,$control->comentarios[0]->observaciones_institucion);
				$rowNumber++;
			}
		}
		$objPHPExcel->getActiveSheet()->setTitle("Controles");
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword('ebb7e6669f5f547adb0b0b5dd349d524686276f3');
		/* Fin hoja2 */

		/* Guardar excel en disco */
		$nombre_archivo = 'uploads/cierre/informe-cumplimiento-'.Auth::user()->institucion->id.'.xls';
		if(!is_dir("uploads/cierre"))
			mkdir("uploads/cierre");
	    
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($nombre_archivo);
		return $nombre_archivo;
	}

	public function getInformeCumplimiento(){
		return Response::download('uploads/cierre/informe-cumplimiento-'.Auth::user()->institucion_id.'.xls');
	}

	public function cargaPlanilla(){
		$this->layout->title= "Carga de controles";
        $this->layout->content = View::make('controles/cargar');
	}
	//Actualizacion de codigo indicador y codigo servicio para instituciones
	public function uploadPlanilla(){
		$file = array('excel' => Input::file('excel'));
		$rules = array('excel' => 'required',);
		$validator = Validator::make($file, $rules);
		if ($validator->fails()) {
			return Redirect::to('instituciones/carga')->withInput()->withErrors($validator);
		}
		else {
			if (Input::file('excel')->isValid()) {
				$destinationPath = 'uploads';
				$extension = Input::file('excel')->getClientOriginalExtension();
				$name = Input::file('excel')->getClientOriginalName();
				$fileName = $name;
				Input::file('excel')->move($destinationPath, $fileName);
				//Cargar excel en tabla
				$archivo = $destinationPath."/".$fileName;
				$inputFileType = PHPExcel_IOFactory::identify($archivo);
				$objReader= PHPExcel_IOFactory::createReader($inputFileType);
				$objReader->setReadDataOnly(true);
				$objPHPExcel=$objReader->load($archivo);
				$objWorksheet = $objPHPExcel->getActiveSheet();
				$rows = $objPHPExcel->getActiveSheet()->getHighestRow();
				for($fila=0;$fila<=$rows;$fila++){
					$institucion = Institucion::where('id',$objWorksheet->getCellByColumnAndRow(0,$fila)->getValue())->first();
					if(!is_null($institucion)){
						$institucion->codigo_indicador = $objWorksheet->getCellByColumnAndRow(3,$fila)->getValue();
						$institucion->codigo_servicio = $objWorksheet->getCellByColumnAndRow(3,$fila)->getValue();
						$institucion->save();
					}else{
						echo $objWorksheet->getCellByColumnAndRow(1, $fila)->getValue()."---".$objWorksheet->getCellByColumnAndRow(3, $fila)->getValue()."<br>";
					}
				}
				Session::flash('success', 'Carga exitosa');
				return Redirect::to('instituciones/carga');
			}else {
				Session::flash('error', 'Archivo invalido');
				return Redirect::to('instituciones/carga');
			}
		}
	}

}
