<?php

class File extends Eloquent {

	protected $table = 'files';

	public function instituciones(){
        return $this->belongsTo('Institucion');
    }

    public function controles(){
        return $this->belongsTo('Control');
    }

}