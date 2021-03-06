<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Usuario extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $table = 'usuarios';
	protected $hidden = array('remember_token');

	public function institucion(){
        return $this->belongsTo('Institucion');
    }

    public function scopeParticipantes($query){
        return $query->whereIn('perfil', array('ingreso','validador'));
    }

}