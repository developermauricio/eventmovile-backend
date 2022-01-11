<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class MeetChatPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'meetChat.index']);
        Permission::create(['name' => 'meetChat.show']);
        Permission::create(['name' => 'meetChat.store']);
        Permission::create(['name' => 'meetChat.update']);
        Permission::create(['name' => 'meetChat.destroy']);

        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('meetChat.index');
        $guest->givePermissionTo('meetChat.show');
        $guest->givePermissionTo('meetChat.store');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('meetChat.index');
        $admin->givePermissionTo('meetChat.show');
        $admin->givePermissionTo('meetChat.store');
        $admin->givePermissionTo('meetChat.update');
        $admin->givePermissionTo('meetChat.destroy');

        $business = Role::findOrFail(4);
        $business->givePermissionTo('meetChat.index');
        $business->givePermissionTo('meetChat.show');
        $business->givePermissionTo('meetChat.store');
        $business->givePermissionTo('meetChat.update');
        $business->givePermissionTo('meetChat.destroy');
    }
}
