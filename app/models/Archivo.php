<?php

class Archivo extends Eloquent {

	protected $table = 'files';
	protected $fillable = array('institucion_id', 'control_id', 'filename');

	public function instituciones(){
        return $this->belongsTo('Institucion');
    }

    public function controles(){
        return $this->belongsTo('Control');
    }

}