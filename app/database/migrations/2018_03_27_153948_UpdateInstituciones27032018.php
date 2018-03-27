<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInstituciones27032018 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('instituciones', function($table)
		{
			$table->enum('entidad_analista', array('interior','segpres','subtel'))->after('sigla')->nullable();
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
		    $table->dropColumn('entidad_analista');
		});
	}

}
