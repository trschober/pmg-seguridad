<?php

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
	        						  count(riesgos.id) as cantidad_archivos_riesgo'))
            ->join('instituciones','instituciones.id','=','comentarios.institucion_id')
            ->leftJoin('riesgos','instituciones.id','=','riesgos.institucion_id')
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
	        						  count(riesgos.id) as cantidad_archivos_riesgo'))
            ->join('instituciones','instituciones.id','=','comentarios.institucion_id')
            ->leftJoin('riesgos','instituciones.id','=','riesgos.institucion_id')
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
}