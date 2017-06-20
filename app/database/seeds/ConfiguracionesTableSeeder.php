<?php

class ConfiguracionesTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('configuraciones')->insert(array(
		  	array('nombre' => 'fecha_inicio','valor' => '01-06-2017','tipo' => 'fechas'),
			array('nombre' => 'fecha_termino','valor' => '30-06-2017','tipo' => 'fechas')
		));
	}

}

?>