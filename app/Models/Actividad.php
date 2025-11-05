<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Actividad extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nombre',
//        'tipo',
    ];
}
