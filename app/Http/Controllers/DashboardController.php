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
		$semanas = [];
		$esteMes = Carbon::now()->startOfMonth();
//		$MesPasado = Carbon::now()->addMonth(- 1)->startOfMonth();
		
		$data = Reporte::selectRaw('
        actividad_id,
        WEEK(fecha) as semana,
        AVG(tiempo_transcurrido) as promedio,
        COUNT(*) as cantidad_reportes
	    ')->whereMonth('fecha', '>=', $esteMes)
	      ->with('actividad:id,nombre')
	      ->groupBy('actividad_id', 'semana')->orderBy('actividad_id')->get()->map(function ($row) {
				$this->semanas[] = $row->semana;
				
				return [
					'actividad'         => $row->actividad->nombre ?? 'Sin nombre',
					'cantidad_reportes' => $row->cantidad_reportes,
					'promedio'          => (float)$row->promedio,
				];
			})->toArray()
		;
		
		$disponible = 44*60; //44 semanales * 60 (minutos)
		$data2 = Reporte::selectRaw('
		        user_id,
		        SUM(tiempo_transcurrido) as total_mins
			    ')->whereMonth('fecha', '>=', $esteMes)
			    ->where('user_id', '!=', 1)
		          ->with('trabajador:id,name') // Ajusta el nombre de la relación
		                ->groupBy('user_id')->orderByDesc('total_mins')->limit(15)->get()->map(function ($row) use ($disponible) {
				return [
					'trabajador' => $row->trabajador->name ?? 'Sin nombre',
					'total_mins' => (float)$row->total_mins,
					'disponible' => ($disponible) - (float)$row->total_mins,
					'minimo' => ($disponible),
				];
			})->toArray()
		;
		
		if ($numberPermissions > 1) {
			$contadores = [
				'users'            => User::count(),
				'roles'            => Role::count(),
				'Ordenproduccions' => Ordenproduccion::count(),
				'reportes'         => Reporte::count(),
				'permissions'      => Permission::count(),
			];
			
			return Inertia::render('Dashboard', [
				'contadores'        => $contadores,
				'acquisitionsData'  => $data,
				'acquisitionsData2' => $data2,
				'semanas'           => $this->semanas,
			]);
		}
		else {
			return redirect()->route('reporte.index');
		}
	}
}
