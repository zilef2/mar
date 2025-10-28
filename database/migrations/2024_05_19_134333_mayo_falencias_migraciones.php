<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     php artisan migrate --path=/database/migrations/2024_05_19_134333_mayo_falencias_migraciones.php
     php artisan migrate:rollback --path=/database/migrations/2024_05_19_134333_mayo_falencias_migraciones.php
     * 
     sudo php artisan migrate --path=/database/migrations/2024_05_19_134333_mayo_falencias_migraciones.php
     */
    public function up(): void
    {
        Schema::table('guardar_google_sheets_comercials', function (Blueprint $table) {
            $table->string('fecha_inicio_fabricacion')->nullable();
        });

        Schema::table('reportes', function (Blueprint $table) {
            $table->double('tiempo_transcurrido')->nullable();
        });
        Schema::table('guardar_google_sheets_comercials', function (Blueprint $table) {
            $table->string('fecha_terminacion_fabricacion')->nullable();
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
