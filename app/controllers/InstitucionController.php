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
	   	$fpdf->SetXY(10, 70);
	    $fpdf->Cell(0,10,utf8_decode("Con fecha ".date("d-m-Y").", el Servicio ".Auth::user()->institucion->servicio),0,0,'L');
	   	$fpdf->SetXY(10, 75);
	    $fpdf->Cell(0,10,utf8_decode("ha informado a la presente Red de Expertos, a través de la Plataforma dispuesta por SEGPRES, la siguiente medición del"),0,0,'L');
	    $fpdf->SetXY(10, 80);
	    $fpdf->Cell(0,10,utf8_decode("Indicador de Seguridad de la Información:"),0,0,'L');
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
		$fpdf->Cell(0,10,utf8_decode("validación del indicador de SSI para el periodo 2017."),0,0,'L');
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
}
