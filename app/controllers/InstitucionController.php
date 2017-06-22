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
		            }
				}
				if($faltantes>0){
					$url = URL::to('controles?page='.$i);
		    		return Redirect::to($url);
				}
			}
		}else{
			//enviar a aprobador
			$institucion = Institucion::find(Auth::user()->institucion_id);
			$institucion->estado = 'enviado';
			$institucion->save();
			//envio de mail pendiente
			return Redirect::to('bienvenida');
		}
	}

	public function setRechazo(){
		//rechazar a reporte
		$institucion = Institucion::find(Auth::user()->institucion_id);
		$institucion->estado = 'rechazado';
		$institucion->observaciones_aprobador = Input::has('observaciones') ? Input::get('observaciones') : NULL;
		$institucion->save();
		//envio de mail pendiente
		return Redirect::to('bienvenida');
	}

}
