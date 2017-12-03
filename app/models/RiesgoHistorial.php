<?php

class RiesgoHistorial extends Eloquent {
	
	protected $table = 'riesgos_historial';
	protected $fillable = array('institucion_id','filename');

	public function instituciones(){
        return $this->belongsTo('Institucion');
    }
}