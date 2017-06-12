<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateComentarios12062017 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		DB::statement('ALTER TABLE comentarios CHANGE COLUMN anio_implementacion anio_compromiso VARCHAR(8) NOT NULL');
		Schema::table('comentarios', function($table)
		{
		    $table->string('anio_implementacion',8)->after('anio_compromiso')->nullable();
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
		DB::statement('ALTER TABLE comentarios CHANGE COLUMN anio_compromiso anio_implementacion VARCHAR(8) NOT NULL');
		Schema::table('comentarios', function($table)
		{
		    $table->dropColumn('anio_implementacion');
		});
	}

}
