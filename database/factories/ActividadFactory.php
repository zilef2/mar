<?php

namespace Database\Factories;

use App\Models\Actividad;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ActividadFactory extends Factory {
	
	protected $model = Actividad::class;
	
	public function definition(): array {
		return [
			'nombre'     => $this->faker->word(),
			'codigo'     => $this->faker->word(),
			'tipo'     => $this->faker->word(),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),
		];
	}
}
