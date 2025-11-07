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
		$modelosIniciales = [
			'Centrotrabajo',
			'reporte',
		];
        $superadmin = Role::create(['name' => 'superadmin']);
        $superadmin->givePermissionTo([
            'isSuper',
            'isAdmin',
			'issupervisor',
			'isrevisor',
            'isadministrativo',
			'isempleado',
        ]);
		
        $vectorCRUD = [
			'create', 'update','read','delete'
        ];
		
        $vectorModelo = [
			'user',
			'role',
			'permission',
			'reporte','Centrotrabajo','parametros'
        ];
		
        foreach ($vectorCRUD as $value) { foreach ($vectorModelo as $model) { $superadmin->givePermissionTo([ $value.' '.$model ]); } }

        $admin = Role::create(['name'=> 'Admin' ]);
		unset($vectorCRUD[3]);
		
        $admin->givePermissionTo([
            'isAdmin',
            'isadministrativo',
			'issupervisor',
			'isrevisor',
			'isempleado',
        ]);
        foreach ($vectorCRUD as $value) { foreach ($vectorModelo as $model) { $admin->givePermissionTo([ $value.' '.$model ]); } }
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

            //#Centrotrabajo
            'update Centrotrabajo',
            'read Centrotrabajo',

            //#parametros
            'read parametros',
        ]);
        $supervisor = Role::create(['name' => 'supervisor']);
        $supervisor->givePermissionTo([
            'issupervisor',

            //#user
            'read user',
            // 'create user',
            // 'read role',

            //#reporte
            'read reporte',
            'create reporte',
            'update reporte',
            'delete reporte',

            //#Centrotrabajo
            'update Centrotrabajo',
            'read Centrotrabajo',
        ]);



        // $role->revokePermissionTo($permission);
        // $permission->removeRole($role);
    }
}
