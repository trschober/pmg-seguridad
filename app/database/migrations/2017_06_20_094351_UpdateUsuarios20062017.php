<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsuarios20062017 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE usuarios MODIFY COLUMN perfil ENUM("reporte","aprobador","experto","evaluador")');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE usuarios MODIFY COLUMN perfil ENUM("reporte","aprobador","expertos")');
	}

}
