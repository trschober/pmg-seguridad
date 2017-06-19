<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Archivo extends Eloquent {
	
	use SoftDeletingTrait;
	
	protected $table = 'files';
    protected $dates = ['deleted_at'];
	protected $fillable = array('institucion_id', 'control_id', 'filename');

	public function instituciones(){
        return $this->belongsTo('Institucion');
    }

    public function controles(){
        return $this->belongsTo('Control');
    }

}