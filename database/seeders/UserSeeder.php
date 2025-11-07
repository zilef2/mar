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
			                           'password'          => bcrypt($metdoo1), //   1_IML_2super+-*1_IML_2
			                           'email_verified_at' => date('Y-m-d H:i'), //
			                           'identificacion'    => '135791113',
			                           'celular'           => '123456789'
		                           ]);
		$superadmin->assignRole('superadmin');
		
		$nombresGenericos = [
			'Ashly_maria'   => 777117711,
			'Teresa_yadiza' => 1234567890,
		];
		
		$rolesUser = [
			'administrativo',
			'empleado',
		];
		
		foreach ($rolesUser as $rol) {
			foreach ($nombresGenericos as $key => $value) {
				$yearRandom = (rand(22, 49));
				$nombresRandom = (rand(1, count($rolesUser)));
				$anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');
				$unUsuario = User::create([
					                          'name'              => substr($key, $nombresRandom) . ' el ' . $rol,
					                          'email'             => $key . '@' . $rol . $key . '.com',
					                          'password'          => bcrypt($genPa . ' _ ' . $rol), //1234_modnom _ Empleado
					                          'email_verified_at' => date('Y-m-d H:i'),
					                          
					                          'identificacion' => $value,
					                          'celular'        => ((int)$value) * 2,
					                          
					                          //					                          'fecha_nacimiento'              => 'masculino',
					                          'sexo'           => 'masculino',
					                          'salario'        => 1432000,
					                          'cargo'          => 'cargo ejemplo',
					                          'area'           => 'area ejemplo',
				                          ]);
				$unUsuario->assignRole($rol);
			}
		}
		
	}
}
