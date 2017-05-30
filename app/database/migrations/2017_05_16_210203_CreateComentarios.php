<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComentarios extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('comentarios', function($table) {
			$table->integer('institucion_id')->unsigned();
			$table->foreign('institucion_id')->references('id')->on('instituciones')->onDelete('cascade');
			$table->integer('control_id')->unsigned();
			$table->foreign('control_id')->references('id')->on('controles')->onDelete('cascade');
			$table->enum('cumple', array('si','no'))->nullable();
			$table->text('observaciones_institucion')->nullable();
			$table->text('observaciones_red')->nullable();
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
		//
		Schema::drop('comentarios');
	}

}
