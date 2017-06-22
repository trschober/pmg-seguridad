<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateComentarios20062017 extends Migration {

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
		    $table->string('tipo_formulacion',64)->after('observaciones_red')->nullable();
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
		    $table->dropColumn('tipo_formulacion');
		});
	}

}
