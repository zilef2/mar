<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActividadSeeder extends Seeder {
	
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		//php artisan config:clear ; php artisan cache:clear ; php artisan config:cache ; php artisan migrate:fresh --seed
		
		Actividad::create(['nombre' => 'Corte']);
		Actividad::create(['nombre' => 'Doblez']);
		Actividad::create(['nombre' => 'Pintura']);
		Actividad::create(['nombre' => 'Troquelado']);
		Actividad::create(['nombre' => 'Soldadura']);
		Actividad::create(['nombre' => 'Lavado']);
		Actividad::create(['nombre' => 'Ensamble Electrónico']);
		Actividad::create(['nombre' => 'Ensamble Producto de linea']);
		Actividad::create(['nombre' => 'Almacenaje']);
		Actividad::create(['nombre' => 'Empaque']);
		
	}
}
