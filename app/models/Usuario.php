<?php

class Usuario extends Eloquent {

	protected $table = 'usuarios';

	public function instituciones(){
        return $this->belongsTo('Institucion');
    }

}