<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComentariosHistorial extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comentarios_historial', function($table) {
			$table->increments('id');
			$table->integer('institucion_id')->unsigned();
			$table->foreign('institucion_id')->references('id')->on('instituciones')->onDelete('cascade');
			$table->integer('control_id')->unsigned();
			$table->foreign('control_id')->references('id')->on('controles')->onDelete('cascade');
			$table->string('anio_compromiso', 8)->nullable();
			$table->string('anio_implementacion', 8)->nullable();
			$table->enum('cumple', array('si','no'))->nullable();
			$table->text('observaciones_institucion')->nullable();
			$table->text('observaciones_red')->nullable();
			$table->string('tipo_formulacion',64)->nullable();
			$table->integer('anio')->nullable();
			$table->integer('ejercicio')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('comentarios_historial');
	}

}
