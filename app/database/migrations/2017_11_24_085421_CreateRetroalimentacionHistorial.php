<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetroalimentacionHistorial extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('instituciones_historial', function($table) {
			$table->increments('id');
			$table->integer('institucion_id')->unsigned();
			$table->foreign('institucion_id')->references('id')->on('instituciones')->onDelete('cascade');
			$table->text('observaciones_red')->nullable();
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
		Schema::drop('instituciones_historial');
	}

}
