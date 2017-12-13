<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInstituciones12122017 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('instituciones', function($table)
		{
		    $table->integer('codigo_indicador')->after('observaciones_red')->nullable();
		    $table->integer('codigo_servicio')->after('codigo_indicador')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$table->dropColumn('codigo_indicador');
		$table->dropColumn('codigo_servicio');
	}

}
