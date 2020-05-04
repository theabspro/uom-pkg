<?php
namespace Abs\UomPkg\Database\Seeds;

use App\Permission;
use Illuminate\Database\Seeder;

class UomPkgPermissionSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$permissions = [
			//Uoms
			[
				'display_order' => 99,
				'parent' => null,
				'name' => 'uoms',
				'display_name' => 'UOMs',
			],
			[
				'display_order' => 1,
				'parent' => 'uoms',
				'name' => 'add-uom',
				'display_name' => 'Add',
			],
			[
				'display_order' => 2,
				'parent' => 'uoms',
				'name' => 'edit-uom',
				'display_name' => 'Edit',
			],
			[
				'display_order' => 3,
				'parent' => 'uoms',
				'name' => 'delete-uom',
				'display_name' => 'Delete',
			],

		];
		Permission::createFromArrays($permissions);
	}
}