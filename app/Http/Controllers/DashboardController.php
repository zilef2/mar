<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\Models\Ordenproduccion;
use App\Models\Permission;
use App\Models\Reporte;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller {
	
	public array $semanas = [];
	
	public function Dashboard(Request $request) {
		$numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Dashboard'));
		if ($numberPermissions > 1) {
			
			$year = $request->input('year', Carbon::now()->year);
			$month = $request->input('month', Carbon::now()->month);
			$esteMes = Carbon::create($year, $month, 1)->startOfMonth();
			//		$MesPasado = Carbon::now()->addMonth(- 1)->startOfMonth();
			
			
			//grafico1
			$int_limit = 10;
			$disponible = 44 * 60 * 4; //44 h/semanales * 60 (minutos) = 2640 mins * 4 semanas = 10560 h/mes
			
			//<editor-fold desc="Grafico 1 y 5">
			$data2 = Reporte::join('users', 'users.id', '=', 'reportes.user_id')->selectRaw('
        reportes.user_id,
        users.name as trabajador,
        SUM(reportes.tiempo_transcurrido) as total_mins
        ')->where('fecha', '>=', $esteMes)->where('fecha', '<', $esteMes->copy()->addMonth())->where('reportes.user_id', '!=', 1)->groupBy('reportes.user_id', 'users.name')->orderByDesc('total_mins')->limit($int_limit)->get()->map(function ($row) use ($disponible) {
				$minstrabajados = (float)$row->total_mins;
				$minstrabajados = $minstrabajados * 60;
				
				return [
					'trabajador' => $dosPrimeras = implode(' ', array_slice(explode(' ', trim(($row->trabajador))), 0, 2)),
					'total_mins' => $minstrabajados,
					'disponible' => $disponible - $minstrabajados,
					'minimo'     => $disponible,
				];
			})->toArray();
			
			//grafico5 (1.5)
			$esteMes = Carbon::create($year, $month, 1)->startOfMonth();
			
			$data5 = Reporte::join('users', 'users.id', '=', 'reportes.user_id')->selectRaw('
        reportes.user_id,
        users.name as trabajador,
        SUM(reportes.tiempo_transcurrido) as total_mins
        ')->where('fecha', '>=', $esteMes)->where('fecha', '<', $esteMes
				->copy()->addMonth())->where('reportes.user_id', '!=', 1)->groupBy('reportes.user_id', 'users.name')->orderBy('total_mins')->limit($int_limit)->get()->map(function ($row) use ($disponible) {
					
					$minstrabajados = (float)$row->total_mins;
					$minstrabajados = $minstrabajados * 60;
					
					return [
						'trabajador' => $dosPrimeras = implode(' ', array_slice(explode(' ', trim(($row->trabajador))), 0, 2)),
						'total_mins' => $minstrabajados,
						'disponible' => $disponible - $minstrabajados,
						'minimo'     => $disponible,
					];
				})->toArray()
			;
			//</editor-fold>
			
			
			
			
			//<editor-fold desc="g2">
			$data = Reporte::selectRaw('
        actividad_id,
        AVG(MinutosEstimados) as estimado,
        WEEK(fecha, 1) as semana,
        AVG(tiempo_transcurrido) as promedio,
        COUNT(*) as cantidad_reportes
        ')->where('fecha', '>=', $esteMes)->where('fecha', '<', $esteMes->copy()->addMonth())->with('actividad:id,nombre')->groupBy('actividad_id', 'semana')->orderBy('promedio')->get()->map(function ($row) {
				return [
					'actividad' => ($row->actividad->nombre ?? 'Sin nombre') . ' s' . $row->semana,
					'Estimado'  => (float)$row->estimado,
					'promedio'  => (float)$row->promedio,
				];
			})->toArray();
			//</editor-fold>
			
			//<editor-fold desc="g3">
			$acquisitionsData3 = Reporte::selectRaw('
	        actividad_id,
	        AVG(MinutosEstimados) as estimado,
	        AVG(tiempo_transcurrido) as promedio
	    ')->where('fecha', '>=', $esteMes)->where('fecha', '<', $esteMes->copy()->addMonth())->with('actividad:id,nombre')->groupBy('actividad_id')->orderBy('actividad_id')->get()->map(function ($row) {
				return [
					'actividad' => ($row->actividad->nombre ?? 'Sin nombre'),
					'Estimado'  => (float)$row->estimado,
					'promedio'  => (float)$row->promedio,
				];
			})->toArray();
			//</editor-fold>
			
			
			//<editor-fold desc="g4 - dona">
			$acquisitionsData4 = Reporte::selectRaw('
	        SUM(CASE 
	            WHEN reproceso_id IS NULL AND paro_id IS NULL THEN 1 
	            ELSE 0 
	        END) as reportes,
	
	        SUM(CASE 
	            WHEN reproceso_id IS NOT NULL THEN 1 
	            ELSE 0 
	        END) as reprocesos,
	
	        SUM(CASE 
	            WHEN paro_id IS NOT NULL THEN 1 
	            ELSE 0 
	        END) as paros
	    ')->where('fecha', '>=', $esteMes)->where('fecha', '<', $esteMes->copy()->addMonth())->first();
			//</editor-fold>
			
			
			$promedioDiario = Reporte::whereHas('trabajador.roles', function ($q) {
				$q->whereNotIn('name', ['superadmin', 'admin', 'administrativo']);
			})->whereBetween('fecha', [
				$esteMes,
				$esteMes->copy()->addMonth()
			])->selectRaw('DATE(fecha) as dia, user_id, SUM(tiempo_transcurrido) as horas_dia')
			  ->groupBy('dia', 'user_id')
			  ->get();
			  $promedioDiario = $promedioDiario->avg('horas_dia');
			$porcentajeJornada = round(($promedioDiario / 8) * 100, 1);
			
			return Inertia::render('Dashboard', [
				'year'              => $year,
				'month'             => $month,
				'acquisitionsData'  => $data,
				'acquisitionsData2' => $data2,
				'acquisitionsData3' => $acquisitionsData3,
				'acquisitionsData4' => $acquisitionsData4,
				'acquisitionsData5' => $data5,
				'semanas'           => $this->semanas,
				'int_limit'         => $int_limit,
				'porcentajeJornada'   => $porcentajeJornada,
				'promedioDiario'     => $promedioDiario,
				'bloqueoNoPago'     => false,
			]);
		}
		else {
			return redirect()->route('reporte.index');
		}
	}
}
