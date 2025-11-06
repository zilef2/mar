<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         //pivot table
        Schema::table('reportes', function (Blueprint $table) {
			
			$table->foreign('user_id')->references('id')->on('users')->onDelete('restrict'); //cascade| restrict | set null
            $table->foreign('actividad_id')->references('id')->on('actividads')->onDelete('restrict'); //cascade| restrict | set null
            $table->foreign('reproceso_id')->references('id')->on('reprocesos')->onDelete('restrict'); //cascade| restrict | set null
            $table->foreign('paro_id')->references('id')->on('paros')->onDelete('restrict'); //cascade| restrict | set null
            $table->foreign('ordenproduccion_id')->references('id')->on('ordenproduccions')->onDelete('restrict'); //cascade| restrict | set null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
