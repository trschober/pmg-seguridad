<?php

class InstitucionHistorial extends Eloquent {

	protected $table = 'instituciones_historial';

	public function usuarios(){
        return $this->hasMany('Usuario');
    }

}