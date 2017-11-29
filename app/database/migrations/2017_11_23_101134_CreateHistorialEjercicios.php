<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistorialEjercicios extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('historial_ejercicios', function($table) {
			$table->increments('id');
			$table->integer('anio')->nullable();
			$table->enum('tipo', array('ejercicio','evaluacion'))->nullable();
			$table->boolean('en_curso')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('historial_ejercicios');
	}

}
