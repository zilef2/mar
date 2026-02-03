<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;

use App\helpers\HelpExcel;
use App\Http\Requests\ReporteRequest;
use App\Http\Requests\ReporteUpdateRequest;
use App\Imports\PersonalImport;
use App\Models\Reporte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportesController extends Controller {
	
	public function index(Request $request): Response {
		$permissions = Myhelp::EscribirEnLog($this, ' reportes');
		$numberPermissions = Myhelp::getPermissionToNumber($permissions);
		$perPage = $request->has('perPage') ? $request->perPage : 50;
		
		$reportes = $this->ZounaSearch($request, $perPage, $numberPermissions); //se hace get()
		$empleados = User::WhereHas('roles', function ($query) {
			return $query->whereIn('name', ['supervisor', 'empleado']);
		})->get();
		$empleados = Myhelp::NEW_turnInSelectID($empleados, ' trabajador', 'name');
		
		$total = $reportes->count();
		$losfiltros = [
			'search',
			'field',
			'order',
			'soloTiEstimado',
			'searchdia',
			'search2',
			'search3',
			'search4',
			'search5',
			'search6',
			'search7',
		];
		
		return Inertia::render('reporte/Index', [
			'breadcrumbs'       => [['label' => __('app.label.reporte'), 'href' => route('reporte.index')]],
			'title'             => __('app.label.reporte'),
			'filters'           => $request->all($losfiltros),
			'perPage'           => (int)$perPage,
			'fromController'    => $reportes,
			'total'             => $total,
			'numberPermissions' => $numberPermissions,
			'empleados'         => $empleados,
			'losSelect'         => $this->SelectsMasivos() ?? [],
		]);
	}
	
	public function ZounaSearch($request, $perPage, $numberPermissions) {
		//		$page = (int)(request('page', 1)); // Current page number
		
		if ($numberPermissions > 1) {
			$reportes = Reporte::query();
		}
		else {
			$authid = Myhelp::AuthU()->id;
			$reportes = Reporte::Where('user_id', $authid);
		}
		
		if ($request->has('searchdia')) {
			$reportes = $reportes->WhereDay('fecha', $request->searchdia);
		}
		
		if ($request->has('search2')) {
			$nombre = $request->search2['title'];
			$reportes = $reportes->WhereHas('trabajador', function ($query) use ($nombre) {
				return $query->Where('name', 'like', '%' . $nombre . '%');
			});
		}
		
		if ($request->has('search3')) {
			$tipoReporte = $request->search3['value'];
			
			if ($tipoReporte === 'soloreporte') {
				$reportes->whereNull('paro_id')->whereNull('reproceso_id');
			}
			elseif ($tipoReporte === 'soloparo') {
				$reportes->whereNotNull('paro_id');
			}
			elseif ($tipoReporte === 'soloreproceso') {
				$reportes->whereNotNull('reproceso_id');
			}
		}
		if ($request->has('search4')) {
			$nombreop = $request->search4;
			$reportes = $reportes->WhereHas('ordenproduccion', function ($query) use ($nombreop) {
				return $query->Where('op', 'like', '%' . $nombreop . '%');
			});
		}
		if ($request->has('search5')) {
			$nombreop = $request->search5;
			$reportes = $reportes->WhereHas('actividad', function ($query) use ($nombreop) {
				return $query->Where('nombre', 'like', '%' . $nombreop . '%');
			});
		}
		if ($request->filled('search6') && $request->search6 >= 1 && $request->search6 <= 12) {
//			dd(
//			    (int)$request->search6,
//				$reportes->get(),
//				$reportes->get()[0],
//			);
			$reportes = $reportes->whereMonth('fecha', (int)$request->search6);
		}
		if ($request->has('search7')) {
			$reportes = $reportes->whereYear('fecha', $request->search7);
		}
		if ($request->has('searchDate')) {
			$reportes->where('fecha', $request->searchDate);
		}
		if ($request->has('soloTiEstimado')) {
			$reportes = $reportes->WhereNotnull('MinutosEstimados');
		}
		
//		if (!$request->has('ultimosmeses')) {
//			if ($reportes->count() > 1000) {
//				$BusquedaMenorAMil = Carbon::now()->firstOfMonth()->format('Y-m-d');
//				$reportes = $reportes->whereBetween('fecha', [$BusquedaMenorAMil, now()]);
//				
//				if ($reportes->count() > 2000) {
//					$BusquedaMenorAMil = Carbon::now()->firstOfMonth()->addDays(10)->format('Y-m-d');
//					$reportes = $reportes->whereBetween('fecha', [$BusquedaMenorAMil, now()]);
//				}
//			}
//		}
		
		
		if ($request->has(['field', 'order'])) {
			$reportes = $reportes->orderByRaw('ISNULL(hora_final) DESC')->orderbyDesc('fecha')->orderBy($request->field, $request->order);
		}
		else {
			$reportes = $reportes->orderByRaw('ISNULL(hora_final) DESC')->orderbyDesc('fecha')->orderByDesc('updated_at');
		}
		
		$page = (int)$request->get('page', 1);
		$perPage = (int)$request->get('perPage', 50);
		
		$reportes = $reportes->get();
		
		$reportesPaginados = $reportes->slice(($page - 1) * $perPage, $perPage)->values();
		
		return new LengthAwarePaginator($reportesPaginados, $reportes->count(), $perPage, $page, [
			'path'  => $request->url(),
			'query' => $request->query()
		]);
		//		return $reportes->get();
	}
	
	public function SelectsMasivos(): array { //aproved
		$reporteTemp = new Reporte();
		$atributos_id = $reporteTemp->getFillable();
		$result = [];
		/* 
			0 => "actividad"
			1 => "centrotrabajo"
			2 => "paro"
			3 => "material"
			5 => "ordenproduccion"
			7 => "pieza"
			8 => "reproceso"
			4 => "trabajador"
		*/
		$atributos_solo_id = Myhelp::filtrar_solo_id($atributos_id);
		foreach ($atributos_solo_id as $key => $value) {
			
			$modelInstance = resolve('App\\Models\\' . ucfirst($value));
			$todosResultados = $modelInstance::All();
			$result[$value] = Myhelp::NEW_turnInSelectID($todosResultados, ' ');
			
			if ($value === 'ordenproduccion') {
				$result[$value] = Myhelp::NEW_turnInSelectID($todosResultados, 'a orden', 'op', 'producto_descripcion');
			}
		}
		
		return $result;
	}
	
	public function createdev(Request $request): Response {
		$numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' solo dev createdev'));
		$user = Myhelp::AuthU();
		
		if ($numberPermissions > 1) {
			$reportes = Reporte::query();
		}
		else {
			$reportes = Reporte::Where('user_id', $user->id);
		}
		
		$empleados = User::WhereHas('roles', function ($query) {
			return $query->whereIn('name', ['supervisor', 'empleado']);
		})->get();
		$empleados = Myhelp::NEW_turnInSelectID($empleados, ' trabajador', 'name');
		
		$perPage = $request->has('perPage') ? $request->perPage : 50;
		$total = $reportes->count();
		$page = request('page', 1); // Current page number
		$fromController = new LengthAwarePaginator($reportes->forPage($page, $perPage), $total, $perPage, $page, ['path' => request()->url()]);
		
		return Inertia::render('reporte/Index', [
			'breadcrumbs'       => [['label' => __('app.label.reporte'), 'href' => route('reporte.index')]],
			'title'             => __('app.label.reporte'),
			'filters'           => $request->all(['search', 'field', 'order', 'soloTiEstimado', 'searchdia']),
			'perPage'           => (int)$perPage,
			'total'             => $total,
			'numberPermissions' => $numberPermissions,
			'empleados'         => $empleados,
			'losSelect'         => $this->SelectsMasivos() ?? [],
		]);
	}
	
	//todo: poner esto en modelo Reporte
	//	public function MapearClasePP(&$reportes, $numberPermissions, $valuesGoogleBody): void {
	//		$reportes = $reportes->get()->map(function ($reporte) use ($numberPermissions, $valuesGoogleBody) {
	//			// $reporte->ordenproduccion_s = $valuesGoogleBody->Where('Item_vue',$reporte->ordenproduccion_id)->first()->Item ?? '';
	//			$reporte->actividad_s = $reporte->actividad()->first() !== null ? $reporte->actividad()->first()->nombre : '';
	//			$reporte->centrotrabajo_s = $reporte->centrotrabajo()->first() !== null ? $reporte->centrotrabajo()->first()->nombre : '';
	//			$reporte->trabajador_s = $reporte->trabajador()->first() !== null ? $reporte->trabajador()->first()->name : '';
	//			
	//			$reporte->paro_s = $reporte->paro()->first() !== null ? $reporte->paro()->first()->nombre : '';
	//			$reporte->reproceso_s = $reporte->reproceso()->first() !== null ? $reporte->reproceso()->first()->nombre : '';
	//			
	//			return $reporte;
	//		})->filter();
	//	}
	
	//fin index
	
	public function updatingDate($date) {
		if ($date === null || $date === '1969-12-31') {
			return null;
		}
		
		return date("Y-m-d", strtotime($date));
	}
	
	public function store(ReporteRequest $request) {
		
		$numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'STORE:reportes'));
		$user = Myhelp::AuthU();
		if ($numberPermissions > 1) {
			$userID = $request->user_id ? $request->user_id['value'] : $user->id;
		}
		else {
			$userID = $user->id;
		}
		
		DB::beginTransaction();
		try {
			$Valueparo = null;
			if (isset($request->paro_id['value'])) { //listo(1a) paro
				$Valueparo = $request->paro_id['value'];
			}
			$hoy = date('Y-m-d');
			$tipoFin = $this->getLastReport($hoy, $userID); //BOUNDED 1: primera del dia | 2:intermedia | 3:Ultima del dia
			$tipoReport = $request->tipoReporte['value'];
			
			$reporte = Reporte::create([
				                           'fecha'              => $request->fecha,
				                           'tipoReporte'        => $tipoReport,
				                           'hora_inicial'       => $request->hora_inicial,
				                           'hora_final'         => null,
				                           'user_id'            => $userID,
				                           'actividad_id'       => $request->actividad_id['value'] ?? null,
				                           'paro_id'            => $Valueparo,
				                           'reproceso_id'       => ($request->reproceso_id['value']) ?? null,
				                           'ordenproduccion_id' => ($request->ordenproduccion_id['value']) ?? null,
				                           'tipoFinalizacion'   => $tipoFin,
				                           //BOUNDED 1: primera del dia | 2:intermedia | 3:Ultima del dia
			                           ]);
			DB::commit();
			Myhelp::EscribirEnLog($this, 'STORE:reportes', 'usuario id:' . $user->id . ' | ' . $user->name . ' ha guardado el reporte ' . $reporte->id, false);
			
			return back()->with('success', __('app.label.created_successfully', ['name' => 'Reporte ']));
		} catch (\Throwable $th) {
			DB::rollback();
			Myhelp::EscribirEnLog($this, 'STORE:reportes', false);
			
			return back()->with('error', __('app.label.created_error', ['name' => __('app.label.reporte')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
		}
	}
	
	/*
	 * BOUNDED 1: primera del dia | 2:intermedia | 3:Ultima del dia
	 * Cuando se reporta y hay un reporte sin hora final, se cierra ese reporte
	 */
	private function getLastReport($hoy, $userid) {
		$hoyDate = date_create($hoy);
		date_sub($hoyDate, date_interval_create_from_date_string('1 days'));
		$ayer = date_format($hoyDate, 'Y-m-d');
		$MainQuery = Reporte::Where('user_id', $userid);
		
		$NoTieneReportes = $MainQuery->count() == 0;
		if ($NoTieneReportes) {
			return 1;
		} //primera vez de su vida
		
		//busca el ultimo reporte de hoy
		$ultimoReporte = $MainQuery->Where('fecha', $hoy)->latest()->first();
		
		if ($ultimoReporte === null) { //hoy
			
			$tipo = 1; //primera del dia
			$ultimoReporte = Reporte::Where('user_id', $userid)->latest()->first();
			
			if ($ultimoReporte && $ultimoReporte->hora_final === null) {
				$ultimoReporte->update(['hora_final' => Carbon::now()]);
				$ultimoReporte->HorFinal();
			}
			else {
				//nunca ha reportado
				Myhelp::EscribirEnLog($this, 'getLastReport', 'Primera vez del userid=' . $userid, false);
			}
		}
		else {
			$tipo = 2;
			if ($ultimoReporte->hora_final === null) {
				
				$ultimoReporte->update([
					                       'hora_final' => Carbon::now()
				                       ]);
				$ultimoReporte->HorFinal();
			}
		}
		
		return $tipo;
	}
	
	public function update(ReporteUpdateRequest $request, $id) {
		$user = Myhelp::AuthU();
		$permissions = Myhelp::EscribirEnLog($this, ' UPDATE:reportes');
		$numberPermissions = Myhelp::getPermissionToNumber($permissions);
		DB::beginTransaction();
		try {
			$reporte = Reporte::findOrFail($id);
			if ($request->hora_final === null) {
				$orden = null;
				
				if ($numberPermissions > 8) {
					$actualizar_reporte['codigo'] = $request->codigo == '' ? null : $request->codigo;
					$actualizar_reporte['fecha'] = $request->fecha == '' ? null : $request->fecha;
					$actualizar_reporte['hora_inicial'] = $request->hora_inicial == '' ? null : $request->hora_inicial;
				}
				//				$actualizar_reporte['ordenproduccion_id'] = $request->ordenproduccion_id == null ? null : $orden->id;
				
				if ($request->actividad_id && is_integer($request->actividad_id)) {
					$actualizar_reporte['actividad_id'] = $request->actividad_id;
				}
				else {
					$actualizar_reporte['actividad_id'] = $request->actividad_id['value'];
				}
				
				$actualizar_reporte['paro_id'] = $request->paro_id == null ? null : $request->paro_id;
				$actualizar_reporte['reproceso_id'] = $request->reproceso_id == null ? null : $request->reproceso_id['value'];
				
				//tipoF no va
				$actualizar_reporte['nombreTablero'] = $orden->Nombre_tablero ?? null;
				$actualizar_reporte['OTItem'] = $orden->Item ?? null;
				$actualizar_reporte['MinutosEstimados'] = $request->MinutosEstimados ?? null;
				
			}
			else { //se esta reportando solo la hora fin
				$actualizar_reporte = [];
				if (!$reporte->hora_final) {
					$actualizar_reporte['hora_final'] = $request->hora_final;
				}
				else {
					$actualizar_reporte['hora_inicial'] = $request->hora_inicial ?? null;
					$actualizar_reporte['hora_final'] = $request->hora_final ?? null;
					//					$actualizar_reporte['tiempo_transcurrido'] = $request->tiempo_transcurrido ?? null;
					$actualizar_reporte['fecha'] = $request->fecha ?? null;
				}
			}
			$reporte->update($actualizar_reporte);
			
			if ($request->hora_final !== null) {
				$reporte->HorFinal();
			}
			
			DB::commit();
			Myhelp::EscribirEnLog($this, 'UPDATE:reportes exitoso', 'U:' . $user->name . ' | reporteid: ' . $reporte->id . ' actualizado', false);
			
			return back()->with('success', __('app.label.updated_successfully', ['name' => 'Reporte']));
		} catch (\Throwable $th) {
			DB::rollback();
			Myhelp::EscribirEnLog($this, 'UPDATE:reportes', 'usuario id:' . $user->id ?? ' nottuser ' . ' |reporteid: ' . ($reporte->id ?? 'nottreporte') . '  fallo en el actualizado', false);
			
			return back()->with('error', __('app.label.updated_error', ['name' => ($reporte->id ?? 'no id')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
		}
	}
	
	//fin store functions
	
	public function create() {}
	
	public function show($id) {}
	
	public function edit($id) {}
	
	public function destroy(reporte $reporte) {
		$Numberpermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'DELETE:reportes'));
		try {
			if ($Numberpermissions < 2 && $reporte->hora_final !== null) {
				return back()->with('warn', 'Este reporte ya esta finalizado, consulte con un supervisor');
			}
			
			$reporte->delete();
			Myhelp::EscribirEnLog($this, 'DELETE:reportes', 'usuario id:' . $reporte->id . ' | ' . $reporte->codigo . ' borrado', false);
			
			return back()->with('success', __('app.label.deleted_successfully', ['name' => 'Reporte']));
		} catch (\Throwable $th) {
			Myhelp::EscribirEnLog($this, 'DELETE:reportes', 'usuario id:' . $reporte->id . ' | ' . $reporte->codigo . ' fallo en el borrado:: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile(), false);
			
			return back()->with('error', __('app.label.deleted_error', ['name' => $reporte->codigo]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
		}
	}
	
	public function destroyBulk(Request $request) {
		try {
			$reporte = Reporte::whereIn('id', $request->id);
			$reporte->delete();
			
			return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.reporte')]));
		} catch (\Throwable $th) {
			return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.reporte')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
		}
	}
	
	//FIN : STORE - UPDATE - DELETE
	
	public function subirexceles() { //just  a view
		$permissions = Myhelp::EscribirEnLog($this, ' reporte');
		$numberPermissions = Myhelp::getPermissionToNumber($permissions);
		
		return Inertia::render('reporte/subirExceles', [
			'breadcrumbs' => [['label' => __('app.label.reporte'), 'href' => route('reporte.index')]],
			'title'       => __('app.label.reporte'),
			'numUsuarios' => count(Reporte::all()) - 1,
			// 'UniversidadSelect'   => Universidad::all()
		]);
	}
	
}
