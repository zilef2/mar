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
        Schema::create('ordenproduccions', function (Blueprint $table) {
            $table->id();
//            $table->string('nombre');
            $table->integer('cantidad_minutos')->nullable();
			$table->integer('pedido')->nullable();
			$table->string('op')->nullable();
			$table->string('cliente')->nullable();
			$table->string('obra')->nullable(); 
			$table->string('contrato')->nullable();
			$table->string('producto_descripcion')->nullable();
			$table->string('estado')->nullable();
			$table->string('asesor')->nullable();
			$table->date('fecha_solicitud')->nullable();
			
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenproduccions');
    }
};
            
