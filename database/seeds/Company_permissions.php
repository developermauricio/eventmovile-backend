<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class Company_permissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'company.index']);
        Permission::create(['name' => 'company.show']);
        Permission::create(['name' => 'company.store']);
        Permission::create(['name' => 'company.update']);
        Permission::create(['name' => 'company.destroy']);
        Permission::create(['name' => 'companys-by-id.show']);
    }
}
