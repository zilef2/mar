<?php

namespace Database\Factories;

use App\Models\Actividad;
use App\Models\Ordenproduccion;
use App\Models\Paro;
use App\Models\Reproceso;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reporte>
 */
class ReporteFactory extends Factory {
	
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition() {
		$IDusersArray = User::pluck('id')->toArray();
		$IDrepoArray = Reproceso::pluck('id')->toArray();
		$IDparoArray = Paro::pluck('id')->toArray();
		$IDordenArray = Ordenproduccion::pluck('id')->toArray();
		$IDactiArray = Actividad::pluck('id')->toArray();
		
		$fecha = Carbon::now()->format('Y-m-d');
		
		// hora_inicial aleatoria
		$horaInicial = $this->faker->time('H:i:s');
		$horaInicialCarbon = Carbon::createFromFormat('H:i:s', $horaInicial);
		
		// hora_final: siempre posterior a hora_inicial (añade entre 1 y 480 minutos)
		$minutesToAdd = $this->faker->numberBetween(1, 480);
		$horaFinalCarbon = $horaInicialCarbon->copy()->addMinutes($minutesToAdd);
		$horaFinal = $horaFinalCarbon->format('H:i:s');
		
		// tiempo_transcurrido como resta en formato H:i:s
		$segundosTranscurridos = $horaInicialCarbon->diffInSeconds($horaFinalCarbon);
		$tiempoTranscurrido = ($segundosTranscurridos / 60); // en horas
		
		// otros campos aleatorios
		$tipoFinalizacion = $this->faker->randomElement([1, 2, 3]); // 1: primera del dia, 2: intermedia, 3: ultima
		$tipoReporte = $this->faker->randomElement([0,1,2]); // 0: actividad, 1: reproceso, 2: paro
		
		return [
			'user_id'            => $this->faker->randomElement($IDusersArray),
			'actividad_id'       => $this->faker->randomElement($IDactiArray),
			'reproceso_id'       => $this->faker->randomElement($IDrepoArray),
			'paro_id'            => $this->faker->randomElement($IDparoArray),
			'ordenproduccion_id' => $this->faker->randomElement($IDordenArray),
			
			'fecha'            => $fecha,
			'hora_inicial'     => $horaInicial,
			'hora_final'       => $horaFinal,
			'tipoFinalizacion' => $tipoFinalizacion,
			'tipoReporte'      => $tipoReporte,
			
			'tiempo_transcurrido' => $tiempoTranscurrido,
			'MinutosEstimados'      => 0,
		];
	}
}
