<?php

class Riesgo extends Eloquent {
	
	protected $table = 'riesgos';
	protected $fillable = array('institucion_id','filename');

	public function instituciones(){
        return $this->belongsTo('Institucion');
    }
}