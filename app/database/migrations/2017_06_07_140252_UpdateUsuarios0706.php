<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsuarios0706 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE usuarios MODIFY COLUMN perfil ENUM("reporte","aprobador","expertos")');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE usuarios MODIFY COLUMN perfil ENUM("reporte","aprobador","red de expertos")');
	}

}
