<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuardarGoogleSheetsComercial extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'HayTiemposEstimados', //default = 0, significa que la tabla no ningun valor de tiempo
        'Grupo', //se suele poner la fecha
        'user_id',

        'Nombre_tablero',
        'Item',
        'Item_vue',
        'avance',

        'Tiempo_estimado_corte', //1 *
        'Tiempo_estimado_doblez', //2 *
        'Tiempo_estimado_soldadura', //3 *
        'Tiempo_estimado_pulida', //4 *
        'Tiempo_estimado_ensamble', //5 *4
        'Tiempo_estimado_cobre', //6  *contador=5
        'Tiempo_estimado_cableado', //7 *4
        'Tiempo_estimado_Ing_mec', //8 *
        'Tiempo_estimado_Ing_elec', //9 *
        'fecha_inicio_fabricacion', //10
        'fecha_terminacion_fabricacion', //19jun2024 - solo fecha paso3
        'Tiempo_estimado_FM', //27feb2025
        'Tiempo_Amarillado', //19marzo2025
        'Tiempo_pruebas', //19marzo2025
        //aquitambien
    ];
}
//FALTA:: fecha terminacion AV
