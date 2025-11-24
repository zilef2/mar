<?php

use App\Models\Permission;
use App\Models\Role;
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
        $admin = Role::Where(['name' => 'admin'])->first();
        $administra = Role::Where(['name' => 'administrativo'])->first();
		$vectorListas = [
			 'Reporte',
	        'Ordenproduccion',
	        'Paro',
	        'Reproceso',
	        'Actividad',
		];
        foreach ($vectorListas as $model) { 
			$admin->givePermissionTo([ 'delete '.$model ]); 
			$administra->givePermissionTo([ 'delete '.$model ]); 
			$admin->givePermissionTo([ 'update '.$model ]); 
			$administra->givePermissionTo([ 'update '.$model ]); 
		} 
		$administra->revokePermissionTo([ 'update Reporte']); 
		
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
