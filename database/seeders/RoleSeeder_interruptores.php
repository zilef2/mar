<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder2026 extends Seeder {
	
	/**
	 * Run the database seeds.
	 *
	 * php artisan db:seed --class=RoleSeeder2025
	 * @return void
	 */
	public function run(): void {
		$vectorModelo = ['Interruptores'];
		$vectorCRUD = ['create', 'update', 'read', 'delete', 'update2'];
		foreach ($vectorCRUD as $value) {
			foreach ($vectorModelo as $model) {
				Permission::create(['name' => $value . ' ' . $model]);
			}
		}
		$superadmin = Role::Where(['name' => 'superadmin'])->first();
		$admin = Role::Where(['name' => 'admin'])->first();
		$administrativo = Role::Where(['name' => 'administrativo'])->first();
		foreach ($vectorCRUD as $value) {
			foreach ($vectorModelo as $model) {
				$superadmin->givePermissionTo([$value . ' ' . $model]);
				$admin->givePermissionTo([$value . ' ' . $model]);
				$administrativo->givePermissionTo([$value . ' ' . $model]);
			}
		}
		
		// $role->revokePermissionTo($permission);
		// $permission->removeRole($role);
	}
}
