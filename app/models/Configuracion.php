<?php

class Configuracion extends Eloquent {

	protected $table = 'configuraciones';
	protected $fillable = array('nombre','valor');

	public function scopeFechas($query){
        return $query->where('tipo','fechas');
    }

}