<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ActivityChatPermissionsSeeder extends Seeder
{

    public function run()
    {
        Permission::create(['name' => 'activityChat.index']);
        Permission::create(['name' => 'activityChat.show']);
        Permission::create(['name' => 'activityChat.store']);
        Permission::create(['name' => 'activityChat.update']);
        Permission::create(['name' => 'activityChat.destroy']);
    }
}
