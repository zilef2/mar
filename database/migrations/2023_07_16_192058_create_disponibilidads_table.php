<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateparosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            
            $table->timestamps();

        });
        Schema::create('centrotrabajo_paro', function (Blueprint $table) {
            $table->id();
            // $table->integer('Acti_dispo_repro')->nullable();
            $table->unsignedBigInteger('paro_id');
            $table->foreign('paro_id')
                ->references('id')
                ->on('paros')
                ->onDelete('restrict'); //restrict | set null 
            $table->unsignedBigInteger('centrotrabajo_id');
            $table->foreign('centrotrabajo_id')
                ->references('id')
                ->on('centrotrabajos')
                ->onDelete('restrict'); //restrict | set null  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paros');
    }
}
