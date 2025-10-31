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
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ReportesController extends Controller {
	
	// public $valuesGoogleCabeza, $valuesGoogleBody;
	
	public function index(Request $request): \Inertia\Response {
		ini_set('max_execution_time', 600);//10mims
		$permissions = Myhelp::EscribirEnLog($this, ' reportes');
		$numberPermissions = Myhelp::getPermissionToNumber($permissions);
		$user = Myhelp::AuthU();
		
//		$readGoogle = new ReadGoogleSheets();
//		[$valuesGoogleCabeza, $valuesGoogleBody] = $readGoogle->GetValuesFromSheets();
		//dd($valuesGoogleBody);
		if ($numberPermissions > 1) {
			$reportes = Reporte::query();
		}
		else {
			$reportes = Reporte::Where('operario_id', $user->id);
		}
		
		$this->ZounaSearch($request, $reportes); //se hace get()
		
		$empleados = User::WhereHas('roles', function ($query) {
			return $query->whereIn('name', ['supervisor', 'empleado']);
		})->get();
		$empleados = Myhelp::NEW_turnInSelectID($empleados, ' operario', 'name');
		
		$perPage = $request->has('perPage') ? $request->perPage : 50;
		$total = $reportes->count();
		$page = request('page', 1); // Current page number
		$fromController = new LengthAwarePaginator($reportes->forPage($page, $perPage), $total, $perPage, $page, ['path' => request()->url()]);
		
		return Inertia::render('reporte/Index', [
			'breadcrumbs'        => [['label' => __('app.label.reporte'), 'href' => route('reporte.index')]],
			'title'              => __('app.label.reporte'),
			'filters'            => $request->all(['search', 'field', 'order', 'soloTiEstimado', 'searchdia']),
			'perPage'            => (int)$perPage,
			'fromController'     => $fromController,
			'total'              => $total,
			'numberPermissions'  => $numberPermissions,
			'empleados'       => $empleados,
			'losSelect'          => $this->SelectsMasivos() ?? [],
			'valuesGoogleCabeza' => $valuesGoogleCabeza ?? [],
			'valuesGoogleBody'   => $valuesGoogleBody ?? [],
		]);
	}
	
	public function ZounaSearch($request, &$reportes): void {
		if ($request->has('searchdia')) {
			$reportes = $reportes->WhereDay('fecha', $request->searchdia);
		}
		
		if ($request->has('search2')) {
			$nombre = $request->search2['title'];
			$reportes = $reportes->WhereHas('operario', function ($query) use ($nombre) {
				return $query->Where('name', 'like', '%' . $nombre . '%');
			});
		}
		
		if ($request->has('search3')) {
			$tipoReporte = $request->search3['value'];
			
			if ($tipoReporte === 'soloreporte') {
				$reportes->whereNull('disponibilidad_id')->whereNull('reproceso_id');
			}
			elseif ($tipoReporte === 'solodisponibilidad') {
				$reportes->whereNotNull('disponibilidad_id');
			}
			elseif ($tipoReporte === 'soloreproceso') {
				$reportes->whereNotNull('reproceso_id');
			}
		}
		if ($request->has('search4')) {
			$nombreot = $request->search4;
			$reportes = $reportes->Where('OTItem', 'like', '%' . $nombreot . '%');
		}
		
		if ($request->has('searchDate')) {
			$reportes->where('fecha', $request->searchDate);
		}
		if ($request->has('soloTiEstimado')) {
			$reportes = $reportes->WhereNotnull('TiempoEstimado');
		}
		if ($request->has('FiltroCentro')) {
			$FiltroCentro = $request->FiltroCentro['value'];
			$reportes = $reportes->WhereHas('centrotrabajo', function ($query) use ($FiltroCentro) {
				return $query->Where('id', $FiltroCentro);
			});
		}
		if (!$request->has('ultimosmeses')) {
			if ($reportes->count() > 1000) {
				$BusquedaMenorAMil = Carbon::now()->firstOfMonth()->format('Y-m-d');
				$reportes = $reportes->whereBetween('fecha', [$BusquedaMenorAMil, now()]);
				if ($reportes->count() > 2000) {
					$BusquedaMenorAMil = Carbon::now()->firstOfMonth()->addDays(10)->format('Y-m-d');
					$reportes = $reportes->whereBetween('fecha', [$BusquedaMenorAMil, now()]);
					//                    $reportes = Cache::remember('users', 600, function ()use ($BusquedaMenorAMil,$reportes) {return $reportes->whereBetween('fecha', [$BusquedaMenorAMil, now()]);});
					//$value = Cache::pull('key');
				}
			}
		}
		
		if ($request->has(['field', 'order'])) {
			$reportes = $reportes->orderByRaw('ISNULL(hora_final) DESC')->orderbyDesc('fecha')->orderBy($request->field, $request->order);
		}else {
			$reportes = $reportes->orderByRaw('ISNULL(hora_final) DESC')->orderbyDesc('fecha')->orderByDesc('updated_at');
		}
		
		$reportes = $reportes->get();
	}
	
	public function MapearClasePP(&$reportes, $numberPermissions, $valuesGoogleBody): void {
		$reportes = $reportes->get()->map(function ($reporte) use ($numberPermissions, $valuesGoogleBody) {
			// $reporte->ordentrabajo_s = $valuesGoogleBody->Where('Item_vue',$reporte->ordentrabajo_id)->first()->Item ?? '';
			$reporte->actividad_s = $reporte->actividad()->first() !== null ? $reporte->actividad()->first()->nombre : '';
			$reporte->centrotrabajo_s = $reporte->centrotrabajo()->first() !== null ? $reporte->centrotrabajo()->first()->nombre : '';
			$reporte->operario_s = $reporte->operario()->first() !== null ? $reporte->operario()->first()->name : '';
			
			$reporte->disponibilidad_s = $reporte->disponibilidad()->first() !== null ? $reporte->disponibilidad()->first()->nombre : '';
			$reporte->reproceso_s = $reporte->reproceso()->first() !== null ? $reporte->reproceso()->first()->nombre : '';
			
			return $reporte;
		})->filter();
	}
	
	public function SelectsMasivos(): array {
		$reporteTemp = new Reporte();
		$atributos_id = $reporteTemp->getFillable();
		$result = [];
		/* 
			0 => "actividad"
			1 => "centrotrabajo"
			2 => "disponibilidad"
			3 => "material"
			5 => "ordentrabajo"
			7 => "pieza"
			8 => "reproceso"
			4 => "operario"
		*/
		$atributos_solo_id = Myhelp::filtrar_solo_id($atributos_id);
		foreach ($atributos_solo_id as $key => $value) {
			
			if ($value === 'operario' || $value === 'calendario') {
				continue;
			}
			
			$modelInstance = resolve('App\\Models\\' . ucfirst($value));
			$ultima = $modelInstance::All();
			$result[$value] = Myhelp::NEW_turnInSelectID($ultima, ' ');
			
			if ($value === 'centrotrabajo') {//filtrar las actividades que pertenecen al ct
				foreach ($ultima as $key2 => $val) {
					$actis = $val->Actividads;
					$result[$value . $val->nombre] = Myhelp::NEW_turnInSelectID($actis, ' ');
				}
			}
		}
		
		return $result;
	}
	
	//fin index
	
	public function updatingDate($date) {
		if ($date === null || $date === '1969-12-31') {
			return null;
		}
		
		return date("Y-m-d", strtotime($date));
	}
	
	public function store(ReporteRequest $request) {
		
		$user = Myhelp::AuthU();
		$numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, 'STORE:reportes'));
		if ($numberPermissions > 1) {
			$userID = $request->user_id ? $request->user_id['value'] : $user->id;
		}
		else {
			$userID = $user->id;
		}
		
		DB::beginTransaction();
		try {
			$ValueDisponibilidad = null;
			if (isset($request->disponibilidad_id['value'])) { //listo(1a) disponibilidad
				$ValueDisponibilidad = $request->disponibilidad_id['value'];
			}
			//todo: (1a) falta validar si es reproceso. | validar porque disponibilidades mandan OTitem
			$hoy = date('Y-m-d');
			$tipoFin = $this->getLastReport($hoy, $userID); //BOUNDED 1: primera del dia | 2:intermedia | 3:Ultima del dia
			$tipoReport = $request->tipoReporte['value'];
			$reporte = Reporte::create([
				                           'fecha'            => $request->fecha,
				                           'tipoReporte'      => $tipoReport,
				                           'hora_inicial'     => $request->hora_inicial,
				                           'hora_final'       => null,
				                           'centrotrabajo_id' => $request->centrotrabajo_id['value'] ?? null,
				                           'operario_id'       => $userID,
				                           'actividad_id'      => $request->actividad_id['value'] ?? null,
				                           'disponibilidad_id' => $ValueDisponibilidad,
				                           'reproceso_id'      => ($request->reproceso_id['value']) ?? null,
				                           'tipoFinalizacion'  => $tipoFin,  //BOUNDED 1: primera del dia | 2:intermedia | 3:Ultima del dia
				                           'nombreTablero'     => $request->nombreTablero,
				                           'OTItem'            => $request->OTItem,
				                           'TiempoEstimado'    => $request->TiempoEstimado,
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
	
	private function getLastReport($hoy, $userid) {
		$hoyDate = date_create($hoy);
		date_sub($hoyDate, date_interval_create_from_date_string('1 days'));
		$ayer = date_format($hoyDate, 'Y-m-d');
		$MainQuery = Reporte::Where('operario_id', $userid);
		
		$NoTieneReportes = $MainQuery->count() == 0;
		if ($NoTieneReportes) {
			return 1;
		} //primera vez de su vida
		
		$ultimoReporte = $MainQuery
			->Where('fecha', $hoy)->latest()->first()
		;//busca el ultimo reporte de hoy
		
		if ($ultimoReporte === null) { //hoy
			
			$ultimoReporte = $MainQuery->Where('fecha', $ayer)->latest()->first();
			$tipo = 1; //primera del dia
			//            if ($ultimoReporte === null) //ayer
			$ultimoReporte = Reporte::Where('operario_id', $userid)->latest()->first();
			
			if ($ultimoReporte && $ultimoReporte->hora_final === null) {
				$ultimoReporte->update([
					                       'hora_final' => Carbon::now()
				                       ]);
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
//				$actualizar_reporte['ordentrabajo_id'] = $request->ordentrabajo_id == null ? null : $orden->id;
				$actualizar_reporte['centrotrabajo_id'] = $request->centrotrabajo_id == null ? null : $request->centrotrabajo_id;
				
				if ($request->actividad_id && is_integer($request->actividad_id)) {
					$actualizar_reporte['actividad_id'] = $request->actividad_id;
				}
				else {
					$actualizar_reporte['actividad_id'] = $request->actividad_id['value'];
				}
				
				$actualizar_reporte['disponibilidad_id'] = $request->disponibilidad_id == null ? null : $request->disponibilidad_id;
				$actualizar_reporte['reproceso_id'] = $request->reproceso_id == null ? null : $request->reproceso_id['value'];
				
				//tipoF no va
				$actualizar_reporte['nombreTablero'] = $orden->Nombre_tablero ?? null;
				$actualizar_reporte['OTItem'] = $orden->Item ?? null;
				$actualizar_reporte['TiempoEstimado'] = $request->TiempoEstimado ?? null;
				
			}
			else { //se esta reportando solo la hora fin
				$actualizar_reporte = [];
				if (!$reporte->hora_final) {
					$actualizar_reporte['hora_final'] = $request->hora_final;
				}
				else {
					$actualizar_reporte['hora_inicial'] = $request->hora_inicial ?? null;
					$actualizar_reporte['hora_final'] = $request->hora_final ?? null;
					$actualizar_reporte['tiempo_transcurrido'] = $request->tiempo_transcurrido ?? null;
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
	
	public function uploadempleados(Request $request) {
		Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar', false);
		$countfilas = 0;
		try {
			if ($request->archivo1) {
				
				$helpExcel = new HelpExcel();
				$mensageWarning = $helpExcel->validarArchivoExcel($request);
				if ($mensageWarning != '') {
					return back()->with('warning', $mensageWarning);
				}
				
				Excel::import(new PersonalImport(), $request->archivo1);
				
				$countfilas = session('CountFilas', 0);
				session(['CountFilas' => 0]);
				
				$MensajeWarning = $this->MensajeWar();
				if ($MensajeWarning !== '') {
					return back()
						->with('success', 'Usuarios nuevos: ' . $countfilas)->with('warning', $MensajeWarning)
					;
				}
				
				Myhelp::EscribirEnLog($this, 'IMPORT:reportes', ' finalizo con exito', false);
				if ($countfilas == 0) {
					return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
				}
				else {
					return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
				}
			}
			else {
				return back()->with('error', __('app.label.op_not_successfully') . ' archivo no seleccionado');
			}
		} catch (\Throwable $th) {
			Myhelp::EscribirEnLog($this, 'IMPORT:reportes', ' Fallo importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile(), false);
			
			return back()->with('error', __('app.label.op_not_successfully') . ' Usuario del error: ' . session('larow')[0] . ' error en la iteracion ' . $countfilas . ' ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
		}
	}
	
	private function MensajeWar() {
		$bandera = false;
		$contares = [
			'contar1',
			'contar2',
			'contar3',
			'contar4',
			'contar5',
			'contarVacios',
		];
		$mensajesWarnings = [
			'#correos Existentes: ',
			'Novedad, error interno: ',
			'#identificacions no numericas: ',
			'#generos distintos(M,F,otro): ',
			'#identificaciones repetidas: ',
			'#filas con celdas vacias: ',
		];
		
		foreach ($contares as $key => $value) {
			$$value = session($value, 0);
			session([$value => 0]);
			$bandera = $bandera || $$value > 0;
		}
		session(['contar2' => - 1]);
		
		$mensaje = '';
		if ($bandera) {
			foreach ($mensajesWarnings as $key => $value) {
				if (${$contares[$key]} > 0) {
					$mensaje .= $value . ${$contares[$key]} . '. ';
				}
			}
		}
		
		return $mensaje;
	}
}
