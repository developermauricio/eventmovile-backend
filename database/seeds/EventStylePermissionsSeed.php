<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EventStylePermissionsSeed extends Seeder
{
    public function run()
    {
        Permission::create(['name' => 'eventStyle.index']);
        Permission::create(['name' => 'eventStyle.show']);
        Permission::create(['name' => 'eventStyle.store']);
        Permission::create(['name' => 'eventStyle.update']);
        Permission::create(['name' => 'eventStyle.destroy']);

        $super = Role::findOrFail(1);
        $super->givePermissionTo('eventStyle.index');
        $super->givePermissionTo('eventStyle.show');
        $super->givePermissionTo('eventStyle.store');
        $super->givePermissionTo('eventStyle.update');
        $super->givePermissionTo('eventStyle.destroy');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('eventStyle.index');
        $admin->givePermissionTo('eventStyle.show');
        $admin->givePermissionTo('eventStyle.store');
        $admin->givePermissionTo('eventStyle.update');
        $admin->givePermissionTo('eventStyle.destroy');

        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('eventStyle.show');

        $staff = Role::findOrFail(5);
        $staff->givePermissionTo('eventStyle.show');
    }
}
