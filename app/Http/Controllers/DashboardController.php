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
	
	public function Dashboard() {
		$numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Dashboard'));
		$esteMes = Carbon::now()->startOfMonth();
		
		//		$MesPasado = Carbon::now()->addMonth(- 1)->startOfMonth();
		
		//grafico1
		$data = Reporte::selectRaw('
        actividad_id,
        AVG(MinutosEstimados) as estimado,
        WEEK(fecha, 1) as semana,
        AVG(tiempo_transcurrido) as promedio,
        COUNT(*) as cantidad_reportes
    ')->where('fecha', '>=', $esteMes)->with('actividad:id,nombre')->groupBy('actividad_id', 'semana')->orderBy('actividad_id')->get()->map(function ($row) {
			return [
				'actividad' => ($row->actividad->nombre ?? 'Sin nombre') . ' s' . $row->semana,
				'Estimado'  => (float)$row->estimado,
				'promedio'  => (float)$row->promedio,
			];
		})->toArray();
		
		//grafico3
		$acquisitionsData3 = Reporte::selectRaw('
        actividad_id,
        AVG(MinutosEstimados) as estimado,
        AVG(tiempo_transcurrido) as promedio
    ')->where('fecha', '>=', $esteMes)->with('actividad:id,nombre')
		                              ->groupBy('actividad_id')
		                              ->orderBy('actividad_id')->get()->map(function ($row) {
			return [
				'actividad' => ($row->actividad->nombre ?? 'Sin nombre'),
				'Estimado'  => (float)$row->estimado,
				'promedio'  => (float)$row->promedio,
			];
		})->toArray();
		
		//grafico2
		$disponible = 44 * 60; //44 h/semanales * 60 (minutos) = 2640 mins
		$data2 = Reporte::selectRaw('
		        user_id,
		        SUM(tiempo_transcurrido) as total_mins
			    ')->Where('fecha', '>=', $esteMes)->where('user_id', '!=', 1)->with('trabajador:id,name') // Ajusta el nombre de la relación
		                ->groupBy('user_id')->orderByDesc('total_mins')->limit(15)->get()->map(function ($row) use ($disponible) {
			return [
				'trabajador' => $row->trabajador->name ?? 'Sin nombre',
				'total_mins' => (float)$row->total_mins,
				'disponible' => ($disponible) - (float)$row->total_mins,
				'minimo'     => ($disponible),
			];
		})->toArray();
		
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
	    ')
	    ->where('fecha', '>=', $esteMes)
	    ->first();
		
		if ($numberPermissions > 1) {
			return Inertia::render('Dashboard', [
				'acquisitionsData'  => $data,
				'acquisitionsData2' => $data2,
				'acquisitionsData3' => $acquisitionsData3,
				'acquisitionsData4' => $acquisitionsData4,
				'semanas'           => $this->semanas,
			]);
		}
		else {
			return redirect()->route('reporte.index');
		}
	}
}
