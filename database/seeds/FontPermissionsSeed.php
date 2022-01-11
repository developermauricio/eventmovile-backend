<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FontPermissionsSeed extends Seeder
{
    
    public function run()
    {
        Permission::create(['name' => 'fonts.index']);
        Permission::create(['name' => 'fonts.show']);
        Permission::create(['name' => 'fonts.store']);
        Permission::create(['name' => 'fonts.update']);
        Permission::create(['name' => 'fonts.destroy']);

        $super = Role::findOrFail(1);
        $super->givePermissionTo('fonts.index');
        $super->givePermissionTo('fonts.show');
        $super->givePermissionTo('fonts.store');
        $super->givePermissionTo('fonts.update');
        $super->givePermissionTo('fonts.destroy');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('fonts.index');
        $admin->givePermissionTo('fonts.show');
        $admin->givePermissionTo('fonts.store');
        $admin->givePermissionTo('fonts.update');
        $admin->givePermissionTo('fonts.destroy');

        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('fonts.show');

        $staff = Role::findOrFail(5);
        $staff->givePermissionTo('fonts.show');
    }
}
