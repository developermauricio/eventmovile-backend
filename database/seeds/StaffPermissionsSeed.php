<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StaffPermissionsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $staff = Role::findOrFail(5);
        $staff->givePermissionTo('staffAccess.index');
        $staff->givePermissionTo('staffAccess.show');
    }
}
