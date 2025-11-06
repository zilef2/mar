<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateReportesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
			$table->string('codigo')->default('NA');
            $table->date('fecha');
            $table->time('hora_inicial')->nullable();
            $table->time('hora_final')->nullable();
            $table->integer('cantidad')->nullable();

			
			$table->integer('tipoFinalizacion')->default(1);
			$table->integer('tipoReporte')->default(1);


			$table->string('TiempoEstimado')->nullable();
            $table->double('tiempo_transcurrido')->nullable();
			

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('actividad_id')->nullable();
            $table->unsignedBigInteger('reproceso_id')->nullable();
			
            $table->unsignedBigInteger('paro_id')->nullable();
            $table->unsignedBigInteger('ordenproduccion_id')->nullable();
            //👀 create_reprocesos_table
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reportes');
    }
}
