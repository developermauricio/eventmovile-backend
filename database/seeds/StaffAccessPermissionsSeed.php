<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StaffAccessPermissionsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['id' => 5,'name' => 'staff']);

        Permission::create(['name' => 'staffAccess.index']);
        Permission::create(['name' => 'staffAccess.show']);
        Permission::create(['name' => 'staffAccess.store']);
        Permission::create(['name' => 'staffAccess.update']);
        Permission::create(['name' => 'staffAccess.destroy']);

        

        $super = Role::findOrFail(1);
        $super->givePermissionTo('staffAccess.index');
        $super->givePermissionTo('staffAccess.show');
        $super->givePermissionTo('staffAccess.store');
        $super->givePermissionTo('staffAccess.update');
        $super->givePermissionTo('staffAccess.destroy');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('staffAccess.index');
        $admin->givePermissionTo('staffAccess.show');
        $admin->givePermissionTo('staffAccess.store');
        $admin->givePermissionTo('staffAccess.update');
        $admin->givePermissionTo('staffAccess.destroy');

        $staff = Role::findOrFail(5);
        $staff->givePermissionTo('staffAccess.index');
        $staff->givePermissionTo('staffAccess.show');
    }
}
