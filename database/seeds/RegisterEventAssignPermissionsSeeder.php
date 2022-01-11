<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RegisterEventAssignPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('registerEvent.index');
        $guest->givePermissionTo('registerEvent.show');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('registerEvent.index');
        $admin->givePermissionTo('registerEvent.show');
        $admin->givePermissionTo('registerEvent.store');
        $admin->givePermissionTo('registerEvent.update');
        $admin->givePermissionTo('registerEvent.destroy');
    }
}
