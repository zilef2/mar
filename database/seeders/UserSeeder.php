<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder {
	
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		//   php artisan config:clear ; php artisan cache:clear ; php artisan config:cache ; php artisan migrate:fresh --seed
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
			dd($genPa . ' | no env, even with comands');//123_demcosuper0.+-*123
		}
		$metdoo1 = $genPa . 'super+-*' . $genPa;
		$superadmin = User::create([
			                           'name'              => 'Superadmin',
			                           'email'             => 'ajelof2+8@gmail.com',
			                           'password'          => bcrypt($metdoo1 . '1'), //
			                           'email_verified_at' => date('Y-m-d H:i'),
			                           'identificacion'    => '135791113',
			                           'celular'           => '123456789'
		                           ]);
		$superadmin->assignRole('superadmin');
		
		$this->generarVariosUsers(2);
		
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
		
		//empelado
		//		User::create([
		//			             'name'           => 'ALVAREZ GUZMAN GERMAN GERARDO',
		//			             'email'          => '71681723',
		//			             'password'       => bcrypt('123'),
		//			             'identificacion' => 71681723,
		//			             'celular'        => '3145808140',
		//			             'sexo'           => 'Masculino',
		//			             'salario'        => 0,
		//			             'cargo'          => 'LAVADOR',
		//			             'area'           => 'Bello',
		//		             ])->assignRole('empleado');
		//		
		//		User::create([
		//			             'name'           => 'BUITRAGO QUICENO JUAN CARLOS',
		//			             'email'          => '1020429596',
		//			             'password'       => bcrypt('123'),
		//			             'identificacion' => 1020429596,
		//			             'celular'        => '3206643147',
		//			             'sexo'           => 'Masculino',
		//			             'salario'        => 0,
		//			             'cargo'          => 'AUXILIAR ALMACEN',
		//			             'area'           => 'CRA 66 # 20F-61 INT 104',
		//		             ])->assignRole('empleado');
		
	}
	
	/**
	 * @param mixed $genPa
	 * @param array $sexos
	 * @return int
	 */
	public static function generarVariosUsers(int $cuantos, mixed $genPa = 'a', array $sexos = ['Masculino', 'Femenino']): int {
		$nombresGenericos = [
			'PersonaPruebas' => 777117711, //0
			'Alejo'          => 123123123, //1
			'madrid'         => 234234234, //2
			'felizzola'      => 345345345, //3
			'maria'          => 567567567, //4
			'josefina'       => 567567568, //5
			'perris'         => 567567569, //6
			'alejandra'      => 567567570, //7
			'absurda'        => 567567571, //8
		];
		if($cuantos > count($nombresGenericos)){
			for($i = 0; $i < $cuantos; $i++) {
				$user = User::create([
					                     'name'              => fake()->name(),
					                     'email'             => fake()->unique()->safeEmail(),
					                     'email_verified_at' => now(),
					                     'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
					                     'remember_token'    => Str::random(10),
				                     ]);
				$user->assignRole('empleado');
			}
			return 1;
		}
		for ($i = 0; $i < $cuantos; $i ++) {
			$sexrandom = rand(0, 1);
			$nombresRandom = (rand(1, count($nombresGenericos)));
			$invertido = array_flip($nombresGenericos);
			$key = $invertido[$nombresGenericos[$i]];
			
			$unUsuario = User::create([
				                          'name'              => substr($key, $nombresRandom) . ' el ' . 'empleado',
				                          'email'             => $key . '@' . 'empleado' . $key . '.com',
				                          'password'          => bcrypt($genPa . ' _ ' . 'empleado'), //1_IML_2 _ empleado
				                          'email_verified_at' => date('Y-m-d H:i'),
				                          'identificacion'    => $nombresGenericos[$i],
				                          'celular'           => ($nombresGenericos[$i]) * 2,
				                          'sexo'              => $sexos[$sexrandom],
				                          'salario'           => 1432000,
				                          'cargo'             => 'Cargo ejemplo',
				                          'area'              => 'Area ejemplo',
			                          ]);
			$unUsuario->assignRole('empleado');
		}
		return 1;
	}
}
