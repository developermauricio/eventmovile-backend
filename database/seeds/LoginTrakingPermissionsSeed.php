<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LoginTrakingPermissionsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'loginTracking.index']);
        Permission::create(['name' => 'loginTracking.show']);
        Permission::create(['name' => 'loginTracking.store']);
        Permission::create(['name' => 'loginTracking.update']);
        Permission::create(['name' => 'loginTracking.destroy']);

        $super = Role::findOrFail(1);
        $super->givePermissionTo('loginTracking.index');
        $super->givePermissionTo('loginTracking.show');
        $super->givePermissionTo('loginTracking.store');
        $super->givePermissionTo('loginTracking.update');
        $super->givePermissionTo('loginTracking.destroy');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('loginTracking.index');
        $admin->givePermissionTo('loginTracking.show');
        $admin->givePermissionTo('loginTracking.store');
        $admin->givePermissionTo('loginTracking.update');
        $admin->givePermissionTo('loginTracking.destroy');

        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('loginTracking.store');
        $guest->givePermissionTo('loginTracking.update');
        $guest->givePermissionTo('loginTracking.destroy');

        $bm = Role::findOrFail(4);
        $bm->givePermissionTo('loginTracking.store');
        $bm->givePermissionTo('loginTracking.update');
        $bm->givePermissionTo('loginTracking.destroy');

        $staff = Role::findOrFail(5);
        $staff->givePermissionTo('loginTracking.store');
        $staff->givePermissionTo('loginTracking.update');
        $staff->givePermissionTo('loginTracking.destroy');
        
        
    }
}
