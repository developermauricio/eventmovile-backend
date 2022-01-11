<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HallAssignPermissionsSeeder extends Seeder
{
    
    public function run()
    {
        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('halls.index');
        $guest->givePermissionTo('halls.show');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('halls.index');
        $admin->givePermissionTo('halls.show');
        $admin->givePermissionTo('halls.store');
        $admin->givePermissionTo('halls.update');
        $admin->givePermissionTo('halls.destroy');
    }
}
