<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('events.index');
        $admin->givePermissionTo('events.show');
        $admin->givePermissionTo('events.store');
        $admin->givePermissionTo('events.update');
        $admin->givePermissionTo('events.destroy');

        $admin->givePermissionTo('activities.index');
        $admin->givePermissionTo('activities.show');
        $admin->givePermissionTo('activities.store');
        $admin->givePermissionTo('activities.update');
        $admin->givePermissionTo('activities.destroy');

        $admin->givePermissionTo('businessMarket.index');
        $admin->givePermissionTo('businessMarket.show');
        $admin->givePermissionTo('businessMarket.store');
        $admin->givePermissionTo('businessMarket.update');
        $admin->givePermissionTo('businessMarket.destroy');

        $admin->givePermissionTo('eventInvitations.index');
        $admin->givePermissionTo('eventInvitations.show');
        $admin->givePermissionTo('eventInvitations.store');
        $admin->givePermissionTo('eventInvitations.update');
        $admin->givePermissionTo('eventInvitations.destroy');

        $admin->givePermissionTo('documents.index');
        $admin->givePermissionTo('documents.show');
        $admin->givePermissionTo('documents.store');
        $admin->givePermissionTo('documents.update');
        $admin->givePermissionTo('documents.destroy');

        $admin->givePermissionTo('urlInvitations.index');
        $admin->givePermissionTo('urlInvitations.show');
        $admin->givePermissionTo('urlInvitations.store');
        $admin->givePermissionTo('urlInvitations.update');
        $admin->givePermissionTo('urlInvitations.destroy');

        $admin->givePermissionTo('guests.index');
        $admin->givePermissionTo('guests.show');
        $admin->givePermissionTo('guests.store');
        $admin->givePermissionTo('guests.update');
        $admin->givePermissionTo('guests.destroy');

        $admin->givePermissionTo('typesActivities.index');
        $admin->givePermissionTo('typesActivities.show');
        $admin->givePermissionTo('typesActivities.store');
        $admin->givePermissionTo('typesActivities.update');
        $admin->givePermissionTo('typesActivities.destroy');

        $admin->givePermissionTo('activitySpeakers.index');
        $admin->givePermissionTo('activitySpeakers.show');
        $admin->givePermissionTo('activitySpeakers.store');
        $admin->givePermissionTo('activitySpeakers.update');
        $admin->givePermissionTo('activitySpeakers.destroy');

        $admin->givePermissionTo('speakers.index');
        $admin->givePermissionTo('speakers.show');
        $admin->givePermissionTo('speakers.store');
        $admin->givePermissionTo('speakers.update');
        $admin->givePermissionTo('speakers.destroy');

        $admin->givePermissionTo('modeActivities.index');
        $admin->givePermissionTo('modeActivities.show');
        $admin->givePermissionTo('modeActivities.store');
        $admin->givePermissionTo('modeActivities.update');
        $admin->givePermissionTo('modeActivities.destroy');

        $admin->givePermissionTo('pollQuestions.index');
        $admin->givePermissionTo('pollQuestions.show');
        $admin->givePermissionTo('pollQuestions.store');
        $admin->givePermissionTo('pollQuestions.update');
        $admin->givePermissionTo('pollQuestions.destroy');

        $admin->givePermissionTo('pollAnswers.index');
        $admin->givePermissionTo('pollAnswers.show');
        $admin->givePermissionTo('pollAnswers.store');
        $admin->givePermissionTo('pollAnswers.update');
        $admin->givePermissionTo('pollAnswers.destroy');

        $admin->givePermissionTo('typeQuestions.index');
        $admin->givePermissionTo('typeQuestions.show');
        $admin->givePermissionTo('typeQuestions.store');
        $admin->givePermissionTo('typeQuestions.update');
        $admin->givePermissionTo('typeQuestions.destroy');

          
    }
}
