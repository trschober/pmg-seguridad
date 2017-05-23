<?php

class Control extends Eloquent {

	protected $table = 'controles';

	public function files(){
        return $this->hasMany('File');
    }

    public function comentarios(){
        return $this->hasMany('Comentario');
    }

}