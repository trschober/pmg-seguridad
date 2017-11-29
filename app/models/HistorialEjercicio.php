<?php

class HistorialEjercicio extends Eloquent {
	protected $table = 'historial_ejercicios';
	protected $fillable = array('anio','ejercicio','en_curso');
}