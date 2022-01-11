<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MeetingPermisions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'meeting.index']);
        Permission::create(['name' => 'meeting.show']);
        Permission::create(['name' => 'meeting.store']);
        Permission::create(['name' => 'meeting.update']);
        Permission::create(['name' => 'meeting.destroy']);
        Permission::create(['name' => 'meetings-by-id.show']);

        Permission::create(['name' => 'meeting-rel-users.index']);
        Permission::create(['name' => 'meeting-rel-users.show']);
        Permission::create(['name' => 'meeting-rel-users.store']);
        Permission::create(['name' => 'meeting-rel-users.update']);
        Permission::create(['name' => 'meeting-rel-users.destroy']);
    }
}
