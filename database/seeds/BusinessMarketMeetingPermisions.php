<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class BusinessMarketMeetingPermisions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bm = Role::findOrFail(4);
        $bm->givePermissionTo('meeting.index');
        $bm->givePermissionTo('meeting.show');
        $bm->givePermissionTo('meeting.store');
        $bm->givePermissionTo('meeting.update');
        $bm->givePermissionTo('meeting.destroy');
        $bm->givePermissionTo('meetings-by-id.show');

        $bm->givePermissionTo('meeting-rel-users.index');
        $bm->givePermissionTo('meeting-rel-users.show');
        $bm->givePermissionTo('meeting-rel-users.store');
        $bm->givePermissionTo('meeting-rel-users.update');
        $bm->givePermissionTo('meeting-rel-users.destroy');
    }
}
