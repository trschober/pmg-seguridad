<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInstituciones22062017 extends Migration {

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
			$table->text('observaciones_aprobador')->after('estado')->nullable();
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
		Schema::table('instituciones', function($table)
		{
		    $table->dropColumn('created_at');
		    $table->dropColumn('updated_at');
		});

	}

}
