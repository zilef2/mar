<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateReprocesosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('reprocesos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            
            $table->timestamps();
        });

		//pivot table
        Schema::table('reportes', function (Blueprint $table) {
            $table->foreign('actividad_id')
                ->references('id')
                ->on('actividads')
                ->onDelete('restrict'); //cascade| restrict | set null
//            $table->foreign('paro_id')
//                ->references('id')
//                ->on('paros')
//                ->onDelete('restrict'); //cascade| restrict | set null


            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); //cascade| restrict | set null
            
            
            $table->foreign('reproceso_id')
                ->references('id')
                ->on('reprocesos')
                ->onDelete('restrict'); //cascade| restrict | set null
            //restrict or cascade
        });
    }

    public function down()
    {
//        Schema::dropIfExists('reprocesos');
//        Schema::dropIfExists('reportes');
    }
}
