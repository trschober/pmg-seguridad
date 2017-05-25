<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateComentarios230517 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('comentarios', function($table)
		{
		    $table->string('anio_implementacion',8)->after('control_id');
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
		Schema::table('comentarios', function($table)
		{
		    $table->dropColumn('anio_implementacion');
		});
	}

}
