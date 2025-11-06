<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reporte extends Model
{
    use HasFactory,SoftDeletes;


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
        'tiempo_transcurrido',
        'tipoFinalizacion', //BOUNDED 1: primera del dia | 2:intermedia | 3:Ultima del dia
        'tipoReporte', //acti, repro,paro
        'nombreTablero',
        'OTItem',
        'TiempoEstimado',
    ];

    // public function reportes() { return $this->hasMany('App\Models\Reporte'); }

    public function actividad(): BelongsTo { return $this->BelongsTo(Actividad::class); }
    public function ordenproduccion(): BelongsTo { return $this->BelongsTo(ordenproduccion::class); }
    public function operario(): BelongsTo { return $this->BelongsTo(User::class, 'user_id'); }

    public function pieza(): BelongsTo { return $this->BelongsTo(Pieza::class); }

    public function paro(): BelongsTo { return $this->BelongsTo(paro::class,'paro_id'); }
    public function reproceso(): BelongsTo { return $this->BelongsTo(Reproceso::class); }


    public function HorFinal() : void{
        if($this->hora_final){
            $horaFinal = Carbon::parse($this->hora_final);
            $horaInicial = Carbon::parse($this->hora_inicial);
            $tiemtras = number_format($horaFinal->diffInSeconds($horaInicial)/3600,3);
            $repor = [
                'tiempo_transcurrido' => $tiemtras
            ];
            $this->update($repor);
        }
    }

    public function HorFinalNoValidacion($horaFinal) : void{
        $horaFinal = Carbon::parse($this->hora_final);
        $horaInicial = Carbon::parse($this->hora_inicial);
        $tiemtras = number_format($horaFinal->diffInSeconds($horaInicial)/3600,3);
        $repor = [
            'hora_final' => $horaFinal,
            'tiempo_transcurrido' => $tiemtras
        ];
        $this->update($repor);
    }

}
