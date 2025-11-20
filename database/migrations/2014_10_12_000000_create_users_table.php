<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('email')->nullable();
			$table->string('password');
			
			$table->string('sexo')->default('Masculino');
			$table->string('celular')->nullable();
			$table->string('identificacion')->nullable();
			$table->string('area')->default('Producción');
			$table->string('cargo')->nullable();
			$table->dateTime('fecha_nacimiento')->nullable();
			$table->bigInteger('salario')->nullable();
			
			$table->timestamp('email_verified_at')->nullable();
			$table->timestamps();
			$table->rememberToken();
			$table->softDeletes();
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('users');
	}
};
