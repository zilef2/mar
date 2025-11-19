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
	public array $semanas  = [];
	
	public function Dashboard() {
		$numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Dashboard'));
		$semanas = [];
		$esteMes = Carbon::now()->startOfMonth();
		$MesPasado = Carbon::now()->addMonth(-1)->startOfMonth();
		$data = Reporte::selectRaw('
			actividad_id,
			WEEK(fecha) as semana,
			AVG(tiempo_transcurrido) as promedio')
			->WhereMonth('fecha', '>=', $esteMes)
		               ->with('actividad:id,nombre')
		               ->groupBy('actividad_id', 'semana')
		               ->orderBy('actividad_id')
		               ->get()->map(function ($row) {
				$this->semanas[] = $row->semana;
				
				return [
					'actividad' => $row->actividad->nombre ?? 'Sin nombre',
					'semana'    => $row->semana,
					'promedio'  => (float)$row->promedio,
				];
			})->toArray();
		$data2 = Reporte::selectRaw('
			actividad_id,
			WEEK(fecha) as semana,
			AVG(tiempo_transcurrido) as promedio')
			->WhereMonth('fecha', '>=', $MesPasado)
			->WhereMonth('fecha', '<', $esteMes)
		               ->with('actividad:id,nombre')
		               ->groupBy('actividad_id', 'semana')
		               ->orderBy('actividad_id')
		               ->get()->map(function ($row) {
				$this->semanas[] = $row->semana;
				
				return [
					'actividad' => $row->actividad->nombre ?? 'Sin nombre',
					'semana'    => $row->semana,
					'promedio'  => (float)$row->promedio,
				];
			})->toArray();
		
		if ($numberPermissions > 1) {
			$contadores = [
				'users'            => User::count(),
				'roles'            => Role::count(),
				'Ordenproduccions' => Ordenproduccion::count(),
				'reportes'         => Reporte::count(),
				'permissions'      => Permission::count(),
			];
			return Inertia::render('Dashboard', [
				'contadores'       => $contadores,
				'acquisitionsData' => $data,
				'semanas'           => $this->semanas,
			]);
		}else return redirect()->route('reporte.index');
	}
}
