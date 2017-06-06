<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsuarios0606 extends Migration {

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
		    $table->string('remember_token',100)->after('perfil');
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
		Schema::table('usuarios', function($table)
		{
		    $table->dropColumn('remember_token');
		    $table->dropColumn('created_at');
		    $table->dropColumn('updated_at');
		});
	}

}
