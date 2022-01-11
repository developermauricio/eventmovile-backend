<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GuestRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('events.index');
        $guest->givePermissionTo('events.show');

        $guest->givePermissionTo('activities.index');
        $guest->givePermissionTo('activities.show');

        $guest->givePermissionTo('businessMarket.index');
        $guest->givePermissionTo('businessMarket.show');

        $guest->givePermissionTo('eventInvitations.index');
        $guest->givePermissionTo('eventInvitations.show');

        $guest->givePermissionTo('documents.index');
        $guest->givePermissionTo('documents.show');

        $guest->givePermissionTo('urlInvitations.update');
        
        $guest->givePermissionTo('guests.show');
        $guest->givePermissionTo('guests.store');
        $guest->givePermissionTo('guests.update');

        $guest->givePermissionTo('typesActivities.index');
        $guest->givePermissionTo('typesActivities.show');
        
        $guest->givePermissionTo('activitySpeakers.index');
        $guest->givePermissionTo('activitySpeakers.show');

        $guest->givePermissionTo('speakers.index');
        $guest->givePermissionTo('speakers.show');

        $guest->givePermissionTo('modeActivities.index');
        $guest->givePermissionTo('modeActivities.show');

        $guest->givePermissionTo('pollQuestions.index');
        $guest->givePermissionTo('pollQuestions.show');

        $guest->givePermissionTo('pollAnswers.index');
        $guest->givePermissionTo('pollAnswers.show');
        $guest->givePermissionTo('pollAnswers.store');

        $guest->givePermissionTo('typeQuestions.index');
        $guest->givePermissionTo('typeQuestions.show');


    }
}
