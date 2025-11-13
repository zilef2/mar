<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteUpdateRequest extends FormRequest {
	
	public function authorize() { return true; }
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules() {
		// $reporteId = $this->route('reporte') ?? null;
		return [
			// 'nombre' => 'nullable',
			// 'codigo' => 'nullable|unique:reportes,codigo,'.$reporteId,
			'codigo'              => 'nullable',
			'fecha'               => 'nullable',
			'hora_inicial'        => 'nullable',
			'hora_final'          => 'nullable|after:hora_inicial',
			'tiempo_transcurrido' => 'nullable',
			'actividad_id'        => 'nullable',
			'cantidad'            => 'nullable',
			'paro_id'   => 'nullable',
			'reproceso_id'        => 'nullable',
			'tipoReporte'         => 'nullable',
			'nombreTablero'       => 'nullable',
			'OTItem'              => 'nullable',
			'MinutosEstimados'      => 'nullable',
		];
	}
}
