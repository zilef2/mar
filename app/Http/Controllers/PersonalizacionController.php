<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PersonalizacionController extends Controller {
	
	public function mocharSeleccionados(Request $request) {
		$selectedIds = $request->input('selectedIds');
		
		if (!empty($selectedIds)) {
			Reporte::whereIn('id', $selectedIds)->CerrarReportes(now());
			
			return back()->with('success', 'Reportes seleccionados cerrados exitosamente.');
		}
		
		return back()->with('error', 'No se proporcionaron IDs para mochar.');
		
	}
	
	public function mochar() {
		$hoyHora = Carbon::now();
		$reportesActivos = Reporte::whereNull('hora_final')->get();
		$numReportes = $reportesActivos->count();
		if ($numReportes > 20) {
			return ' <h1>Demasiados reportes activos (' . $numReportes . '), no se actualizan</h1><p> </p> ';
		}
		
		foreach ($reportesActivos as $index => $reportesActivo) {
			
			if ($reportesActivo->TiempoTranscurrido($hoyHora) >= 8) {
				
				$reportesActivo->CerrarReporte8horas();
			}
			else {
				$reportesActivo->CerrarReporte($hoyHora);
			}
		}
		
		return ' <h1>Reportes actualizados</h1><p> ' . $numReportes . ' Reportes fueron cerrados</p> ';
	}
	
	public function corregirReportesInvalidos() {
		$reportesInvalidos = Reporte::whereColumn('hora_final', '<', 'hora_inicial')->get();
		
		if ($reportesInvalidos->isEmpty()) {
			return ' <h1>No hay reportes inválidos</h1><p></p> ';
		}
		
		foreach ($reportesInvalidos as $reporte) {
			$reporte->CerrarReporte8horas();
		}
		return back()->with('success', ' <h1>Reportes corregidos</h1><p>' . $reportesInvalidos->count() . ' reportes fueron ajustados</p> ');
	}
}
