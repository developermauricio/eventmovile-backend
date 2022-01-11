<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NetworkingPermissionsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'networkings.index']);
        Permission::create(['name' => 'networkings.show']);
        Permission::create(['name' => 'networkings.store']);
        Permission::create(['name' => 'networkings.update']);
        Permission::create(['name' => 'networkings.destroy']);

        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('networkings.index');
        $guest->givePermissionTo('networkings.show');
        $guest->givePermissionTo('networkings.update');
        $guest->givePermissionTo('networkings.store');
        $guest->givePermissionTo('networkings.destroy');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('networkings.index');
        $admin->givePermissionTo('networkings.show');
        $admin->givePermissionTo('networkings.store');
        $admin->givePermissionTo('networkings.update');
        $admin->givePermissionTo('networkings.destroy');
    }
}
