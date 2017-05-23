<?php

class Institucion extends Eloquent {

	protected $table = 'instituciones';

	public function usuarios(){
        return $this->hasMany('Usuario');
    }

}