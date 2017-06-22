<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInstituciones20062017 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('instituciones', function($table)
		{
		    $table->enum('estado', array('ingresado','enviado','rechazado','cerrado'))->after('servicio')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('instituciones', function($table)
		{
		    $table->dropColumn('estado');
		});
	}

}
