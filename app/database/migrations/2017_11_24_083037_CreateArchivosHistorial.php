<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivosHistorial extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('files_historial', function($table) {
			$table->increments('id');
			$table->integer('institucion_id')->unsigned();
			$table->foreign('institucion_id')->references('id')->on('instituciones')->onDelete('cascade');
			$table->integer('control_id')->unsigned();
			$table->foreign('control_id')->references('id')->on('controles')->onDelete('cascade');
			$table->string('filename',512);
			$table->integer('historial_id')->unsigned()->nullable();
			$table->foreign('historial_id')->references('id')->on('historial_ejercicios')->onDelete('cascade');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('files_historial');
	}

}
