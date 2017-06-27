<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiesgos extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('riesgos', function($table) {
			$table->increments('id');
			$table->string('filename',128);
			$table->integer('institucion_id')->unsigned();
			$table->foreign('institucion_id')->references('id')->on('instituciones')->onDelete('cascade');
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
		Schema::drop('riesgos');
	}

}
