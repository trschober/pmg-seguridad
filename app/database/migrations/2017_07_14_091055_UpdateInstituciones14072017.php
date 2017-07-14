<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInstituciones14072017 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('instituciones', function($table)
		{
			$table->text('observaciones_red')->after('observaciones_aprobador')->nullable();
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
		    $table->dropColumn('observaciones_red');
		});
	}

}
