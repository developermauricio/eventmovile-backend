<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StickerPermissionsAsign extends Seeder
{
    public function run()
    {
        $super = Role::findOrFail(1);
        $super->givePermissionTo(Permission::all());

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo(Permission::all());
    }
}
