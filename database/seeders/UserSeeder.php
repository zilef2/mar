<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
	
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		//php artisan config:clear ; php artisan cache:clear ; php artisan config:cache ; php artisan migrate:fresh --seed
		$sexos = ['Masculino', 'Femenino'];
		$genPa = config('app.sap_gen');
		
		if (is_null($genPa)) {
			$comando = 'php artisan optimize:clear';
			$salida = [];
			$codigo_retorno = 0;
			exec($comando, $salida, $codigo_retorno);
			
			if ($codigo_retorno === 0) {
				echo "Comando ejecutado con éxito. Salida:\n";
				echo implode("\n", $salida);
			}
			else {
				echo "Error al ejecutar el comando. Código de retorno: " . $codigo_retorno . "\n";
				echo "Mensaje de error (si está en la salida):\n";
				dd('enviroment is null', $genPa, 'error:clear ', implode("\n", $salida));
			}
		}
		if (!$genPa) {
			dd($genPa . ' | no env, even with comands');
		}
		$metdoo1 = $genPa . 'super+-*' . $genPa;
		$superadmin = User::create([
			                           'name'              => 'Superadmin',
			                           'email'             => 'ajelof2+8@gmail.com',
			                           'password'          => bcrypt($metdoo1 . '1'),
			                           'email_verified_at' => date('Y-m-d H:i'),
			                           'identificacion'    => '135791113',
			                           'celular'           => '123456789'
		                           ]);
		$superadmin->assignRole('superadmin');
		
		$nombresGenericos = [
			'PersonaPruebas' => 777117711,
		];
		
		foreach ($nombresGenericos as $key => $value) {
			$yearRandom = (rand(22, 49));
			$sexrandom = rand(0, 1);
			$nombresRandom = (rand(1, count($nombresGenericos)));
			$unUsuario = User::create([
				                          'name'              => substr($key, $nombresRandom) . ' el ' . 'empleado',
				                          'email'             => $key . '@' . 'empleado' . $key . '.com',
				                          'password'          => bcrypt($genPa . ' _ ' . 'empleado'), //1_IML_2 _ empleado
				                          'email_verified_at' => date('Y-m-d H:i'),
				                          'identificacion'    => $value,
				                          'celular'           => ($value) * 2,
				                          'sexo'              => $sexos[$sexrandom],
				                          'salario'           => 1432000,
				                          'cargo'             => 'Cargo ejemplo',
				                          'area'              => 'Area ejemplo',
			                          ]);
			$unUsuario->assignRole('empleado');
		}
		
		User::create([
			             'name'           => 'Carlos mario Restrepo',
			             'email'          => 'cmrb1509@gmail.com',
			             'password'       => bcrypt('0804'),
			             'identificacion' => 1,
			             'celular'        => 1,
			             'sexo'           => 'Masculino',
			             'salario'        => 1,
			             'cargo'          => 'Cargo ejemplo',
			             'area'           => 'Area ejemplo',
		             ])->assignRole('administrativo');
		
		User::create([
			             'name'           => 'Laura Pineda Valencia',
			             'email'          => 'gerencia@imlelectrica.com',
			             'password'       => bcrypt('Iml2025**'),
			             'identificacion' => 1,
			             'celular'        => 1,
			             'sexo'           => 'Femenino',
			             'salario'        => 1,
			             'cargo'          => 'Administrativo',
			             'area'           => 'Gerencia',
		             ])->assignRole('administrativo');
		
	}
}
