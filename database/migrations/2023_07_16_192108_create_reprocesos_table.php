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

    }

    public function down()
    {
//        Schema::dropIfExists('reprocesos');
//        Schema::dropIfExists('reportes');
    }
}

/*
 *
 */