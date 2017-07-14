<?php

class Documento extends Eloquent {
	protected $table = 'documentos';
	protected $fillable = array('filename');
}