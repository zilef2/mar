<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reporte extends Model {
	
	use HasFactory, SoftDeletes;
	
	protected $appends = [
		'Orden',
		'userino',
		'actividadsini',
		'parou',
		'reprocesou',
	];
	protected $fillable = [
		'user_id',
		'actividad_id',
		'reproceso_id',
		'paro_id',
		'ordenproduccion_id',
		
		'id',
		'fecha',
		'hora_inicial',
		'hora_final',
		'tipoFinalizacion', //BOUNDED 1: primera del dia | 2:intermedia | 3:Ultima del dia
		'tipoReporte', //acti, repro,paro
		
		'tiempo_transcurrido',
		'MinutosEstimados',
	];
	
	public function getOrdenAttribute(): string {return $this->ordenproduccion ? $this->ordenproduccion->nombre : ''; }
	
	public function getuserinoAttribute(): string {return $this->trabajador ? $this->trabajador->name : ''; }
	
	public function getactividadsiniAttribute(): string {return $this->actividad ? $this->actividad->nombre : ''; }
	
	public function getparouAttribute(): string {
		return $this->paro ? $this->paro->nombre : ''; 
	}
	public function getreprocesouAttribute(): string {
		return $this->reproceso ? $this->reproceso->nombre : ''; 
	}
	
	// public function reportes() { return $this->hasMany('App\Models\Reporte'); }
	
	public function actividad(): BelongsTo { return $this->BelongsTo(Actividad::class); }
	
	public function ordenproduccion(): BelongsTo { return $this->BelongsTo(ordenproduccion::class); }
	
	public function trabajador(): BelongsTo { return $this->BelongsTo(User::class, 'user_id'); }
	
	public function paro(): BelongsTo { return $this->BelongsTo(paro::class, 'paro_id'); }
	
	public function reproceso(): BelongsTo { return $this->BelongsTo(Reproceso::class); }
	
	public function HorFinal(): void {
		if ($this->hora_final) {
			$horaFinal = Carbon::parse($this->hora_final);
			$horaInicial = Carbon::parse($this->hora_inicial);
		
			$this->update(['tiempo_transcurrido' => 
				               (double)number_format($horaInicial->diffInSeconds($horaFinal) / 3600, 3)
			              ]);
		}
	}
	
	public function HorFinalNoValidacion($horaFinal): void {
		$horaFinal = Carbon::parse($this->hora_final);
		$horaInicial = Carbon::parse($this->hora_inicial);
		$tiemtras = number_format($horaFinal->diffInSeconds($horaInicial) / 3600, 3);
		$repor = [
			'hora_final'          => $horaFinal,
			'tiempo_transcurrido' => $tiemtras
		];
		$this->update($repor);
	}
	
}
