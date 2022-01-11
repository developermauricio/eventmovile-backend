<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ParticipantsScheduleAsignToBusinessUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bm = Role::findOrFail(4);
        $bm->givePermissionTo('participants.show');
        $bm->givePermissionTo('schedule.show');
    }
}
