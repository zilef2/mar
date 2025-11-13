<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = Role::create(['name' => 'superadmin']);
	    $superadmin->givePermissionTo(['isSuper', 'isAdmin']);
        $admin = Role::create(['name'=> 'Admin' ]);
//		unset($vectorCRUD[3]);
		
        $admin->givePermissionTo(['isAdmin']);
	   
        $vectorCRUD = [
			'create', 'update','read','delete'
        ];
		
        $vectorModelo = [
			'user',
			'role',
			'permission',
	        'parametros',
	        
			'Reporte',
            'Ordenproduccion',
	        'Paro',
	        'Reproceso',
	        'Actividad',
	        
        ];
		
        foreach ($vectorCRUD as $value) { foreach ($vectorModelo as $model) {
			$superadmin->givePermissionTo([ $value.' '.$model ]); 
		}}

        foreach ($vectorCRUD as $value) { foreach ($vectorModelo as $model) { $admin->givePermissionTo([ $value.' '.$model ]); } }
		
        //is todos
		$theBasePermissions = config('app.theBasePermissions');
	    foreach ($theBasePermissions as $index => $the_base_permission) {
		    
		    $superadmin->givePermissionTo('is'.$the_base_permission); //super => isempleado, etc
		    $admin->givePermissionTo('is'.$the_base_permission);
		}
        //no more admins

	    
	    
	    
	    
        $empleado = Role::create(['name' => 'empleado' ]);
        $empleado->givePermissionTo([
             //#Reporte
            'read Reporte',
            'create Reporte',
            'delete Reporte',
        ]);

        $administrativo = Role::create(['name' => 'administrativo']);
        foreach ($vectorModelo as $model) { $administrativo->givePermissionTo([ 'read '.$model ]); } //todos los read
         $administrativo->revokePermissionTo('read parametros');
		$vectorListas = [
			 'Reporte',
	        'Ordenproduccion',
	        'Paro',
	        'Reproceso',
	        'Actividad',
		];
        foreach ($vectorListas as $model) { 
			$administrativo->givePermissionTo([ 'create '.$model ]); 
			$administrativo->givePermissionTo([ 'update '.$model ]); 
		} 
		
        $administrativo->givePermissionTo([
            'isadministrativo',

             //user
            'create user',
            'update user',
            //'delete user',
            //Reporte
            //#parametros
			//'role',
			//'permission',
        ]);



        // $role->revokePermissionTo($permission);
        // $permission->removeRole($role);
    }
}
