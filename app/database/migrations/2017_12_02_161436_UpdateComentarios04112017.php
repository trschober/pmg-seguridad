<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateComentarios04112017 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('comentarios', function($table)
		{
		    $table->text('desc_medio_verificacion')->after('observaciones_institucion')->nullable();
		});
		Schema::table('comentarios_historial', function($table)
		{
		    $table->text('desc_medio_verificacion')->after('observaciones_institucion')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('comentarios', function($table)
		{
		    $table->dropColumn('desc_medio_verificacion');
		});
		Schema::table('comentarios_historial', function($table)
		{
		    $table->dropColumn('desc_medio_verificacion');
		});
	}

}
