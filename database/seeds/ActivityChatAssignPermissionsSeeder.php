<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ActivityChatAssignPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('activityChat.index');
        $guest->givePermissionTo('activityChat.show');
        $guest->givePermissionTo('activityChat.store');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('activityChat.index');
        $admin->givePermissionTo('activityChat.show');
        $admin->givePermissionTo('activityChat.store');
        $admin->givePermissionTo('activityChat.update');
        $admin->givePermissionTo('activityChat.destroy');
    }
}
