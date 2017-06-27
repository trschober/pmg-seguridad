<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsuarios27062017 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE usuarios MODIFY COLUMN perfil ENUM("ingreso","validador","experto","evaluador")');
		Schema::table('usuarios', function($table)
		{
		    $table->string('correo',64)->after('apellidos')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE usuarios MODIFY COLUMN perfil ENUM("reporte","aprobador","experto","evaluador")');
		Schema::table('usuarios', function($table)
		{
		    $table->dropColumn('correo');
		});
	}

}
