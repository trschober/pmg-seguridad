<?php

class Comentario extends Eloquent {

	protected $table = 'comentarios';

	public function instituciones(){
    	return $this->belongsTo('Institucion');
    }

    public function controles(){
        return $this->belongsTo('Control');
    }

    public function scopeActualizados($query){
        return $query->whereIn('cumple', array('si','no'));
    }
}