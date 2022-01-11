<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DataRegisterAssignPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //dataRegisters
        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('dataRegisters.index');
        $guest->givePermissionTo('dataRegisters.show');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('dataRegisters.index');
        $admin->givePermissionTo('dataRegisters.show');
        $admin->givePermissionTo('dataRegisters.store');
        $admin->givePermissionTo('dataRegisters.update');
        $admin->givePermissionTo('dataRegisters.destroy');
    }
}
