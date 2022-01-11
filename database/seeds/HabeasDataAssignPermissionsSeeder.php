<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HabeasDataAssingPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('habeasData.index');
        $guest->givePermissionTo('habeasData.show');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('habeasData.index');
        $admin->givePermissionTo('habeasData.show');
        $admin->givePermissionTo('habeasData.store');
        $admin->givePermissionTo('habeasData.update');
        $admin->givePermissionTo('habeasData.destroy');
    }
}
