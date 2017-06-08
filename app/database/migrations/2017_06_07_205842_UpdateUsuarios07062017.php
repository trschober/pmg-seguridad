<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsuarios07062017 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('usuarios', function($table)
		{
		    $table->string('nombres',100)->after('rut')->nullable();
		    $table->string('apellidos',100)->after('nombres')->nullable();
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
		Schema::table('usuarios', function($table)
		{
		    $table->dropColumn('nombres');
		    $table->dropColumn('apellidos');
		});
	}

}
