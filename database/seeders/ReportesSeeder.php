<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\ordenproduccion;
use App\Models\Reporte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReportesSeeder extends Seeder
{
    /*
      Run the database seeds.
     
     php artisan db:seed --class=ReportesSeeder
     */
    public function run()
    {
        $empleados = User::role('empleado')->get();

        // Si no hay actividades u ordenes, creamos defaults simulados o asumimos que existen.
        $actividadesIds = Actividad::pluck('id')->toArray();
        if (empty($actividadesIds)) {
            $actividadesIds = [1]; 
        }

        $ordenesIds = ordenproduccion::pluck('id')->toArray();
        if (empty($ordenesIds)) {
            $ordenesIds = [1];
        }

        // Mes actual: Abril 2026 (según contexto temporal)
        $year = 2026;
        $month = 4;

        foreach ($empleados as $empleado) {
            // Generar 30 reportes para este mes
            for ($day = 1; $day <= 30; $day++) {
                
                // Hora inicial: alrededor de las 8 AM (8:00 a 8:30)
                $horaInicial = Carbon::create($year, $month, $day, 8, rand(0, 30), 0);
                
                // Duración: Media de 7 horas, varianza de 2 horas (aproximado, rango de 5 a 9 horas)
                $duracionHoras = rand(5, 9);
                $duracionMinutos = rand(0, 59);

                // Hora final
                $horaFinal = $horaInicial->copy()->addHours($duracionHoras)->addMinutes($duracionMinutos);
                
                // Calcular tiempo transcurrido en horas decimales
                $tiempoTranscurrido = round($horaInicial->diffInSeconds($horaFinal) / 3600, 3);

                Reporte::create([
                    'user_id' => $empleado->id,
                    'actividad_id' => $actividadesIds[array_rand($actividadesIds)],
                    'ordenproduccion_id' => $ordenesIds[array_rand($ordenesIds)],
                    'fecha' => $horaInicial->toDateString(),
                    'hora_inicial' => $horaInicial->toTimeString(),
                    'hora_final' => $horaFinal->toTimeString(),
                    'tipoFinalizacion' => 1, // primera del dia por simplificación
                    'tipoReporte' => 0, // actividad por defecto
                    'tiempo_transcurrido' => $tiempoTranscurrido,
                    'MinutosEstimados' => $duracionHoras * 60,
                ]);
            }
        }
    }
}
