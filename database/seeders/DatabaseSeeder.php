<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Ordenproduccion;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
	
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run() {
		
		$this->call([
			            PermissionSeeder::class,
			            RoleSeeder::class,
			            UserSeeder::class,
			            ParametrosSeeder::class,
			            ActividadSeeder::class,
			            OrdenproduccionSeeder::class,
		            ]);
		
		\App\Models\User::factory(2)->create();
		\App\Models\Reporte::factory(102)->create();
	}
}
