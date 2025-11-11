<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$theBasePermissions = [
			'administrativo', //this is very important!!!
			'revisor',
			'empleado',
		];
        Permission::create(['name' => 'isSuper']);
        Permission::create(['name' => 'isAdmin']);
		
	    foreach ($theBasePermissions as $index => $the_base_permission) {
		    
            Permission::create(['name' => 'is'.$the_base_permission]);
		}
        
        $vectorModelo = [
			'permission',
			'role',
	        'parametros',
	        
			'user',
	        'reporte',
	        
//	        'Centrotrabajo',
	        'Ordenproduccion',
	        'Paro',
	        //esto se define el 31octubre2025
        ];
        $vectorCRUD = ['create', 'update','read','delete',
			'update2', 'corregir',
        ];
		
        foreach ($vectorCRUD as $value) {
            foreach ($vectorModelo as $model) {
                Permission::create(['name' => $value.' '.$model]);
            }
        }

    }
}
