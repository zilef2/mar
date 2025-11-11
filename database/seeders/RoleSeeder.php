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
	        
			'reporte','Ordenproduccion','parametros'
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
             //#reporte
            'read reporte',
            'create reporte',
            'delete reporte',
        ]);

        $administrativo = Role::create(['name' => 'administrativo']);
        $administrativo->givePermissionTo([
            'isadministrativo',

             //#user
            'read user',
            'create user',
            // 'read role',
            'update user',

            //#reporte
            'read reporte',
            'delete reporte',

            //#parametros
            'read parametros',
        ]);



        // $role->revokePermissionTo($permission);
        // $permission->removeRole($role);
    }
}
