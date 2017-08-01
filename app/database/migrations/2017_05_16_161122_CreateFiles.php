<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('files', function($table) {
			$table->increments('id');
			$table->integer('institucion_id')->unsigned();
			$table->foreign('institucion_id')->references('id')->on('instituciones')->onDelete('cascade');
			$table->integer('control_id')->unsigned();
			$table->foreign('control_id')->references('id')->on('controles')->onDelete('cascade');
			$table->string('filename',512);
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
		Schema::drop('files');
	}

}
