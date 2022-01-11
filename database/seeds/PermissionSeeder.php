<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Permission::create(['name' => 'users.index']);
        Permission::create(['name' => 'users.show']);
        Permission::create(['name' => 'users.destroy']);
        Permission::create(['name' => 'users.update']);

        Permission::create(['name' => 'roles.index']);
        Permission::create(['name' => 'roles.show']);
        Permission::create(['name' => 'roles.destroy']);
        Permission::create(['name' => 'roles.update']);

        Permission::create(['name' => 'permission.index']);
        Permission::create(['name' => 'permission.show']);
        Permission::create(['name' => 'permission.destroy']);
        Permission::create(['name' => 'permission.update']);

        Permission::create(['name' => 'events.index']);
        Permission::create(['name' => 'events.store']);
        Permission::create(['name' => 'events.show']);
        Permission::create(['name' => 'events.destroy']);
        Permission::create(['name' => 'events.update']);

        Permission::create(['name' => 'activities.index']);
        Permission::create(['name' => 'activities.show']);
        Permission::create(['name' => 'activities.store']);
        Permission::create(['name' => 'activities.destroy']);
        Permission::create(['name' => 'activities.update']);

        Permission::create(['name' => 'businessMarket.index']);
        Permission::create(['name' => 'businessMarket.show']);
        Permission::create(['name' => 'businessMarket.store']);
        Permission::create(['name' => 'businessMarket.destroy']);
        Permission::create(['name' => 'businessMarket.update']);

        Permission::create(['name' => 'eventInvitations.index']);
        Permission::create(['name' => 'eventInvitations.show']);
        Permission::create(['name' => 'eventInvitations.store']);
        Permission::create(['name' => 'eventInvitations.destroy']);
        Permission::create(['name' => 'eventInvitations.update']);

        Permission::create(['name' => 'documents.index']);
        Permission::create(['name' => 'documents.show']);
        Permission::create(['name' => 'documents.store']);
        Permission::create(['name' => 'documents.destroy']);
        Permission::create(['name' => 'documents.update']);

        Permission::create(['name' => 'urlInvitations.index']);
        Permission::create(['name' => 'urlInvitations.show']);
        Permission::create(['name' => 'urlInvitations.store']);
        Permission::create(['name' => 'urlInvitations.destroy']);
        Permission::create(['name' => 'urlInvitations.update']);

        Permission::create(['name' => 'guests.index']);
        Permission::create(['name' => 'guests.show']);
        Permission::create(['name' => 'guests.store']);
        Permission::create(['name' => 'guests.destroy']);
        Permission::create(['name' => 'guests.update']);

        Permission::create(['name' => 'typesActivities.index']);
        Permission::create(['name' => 'typesActivities.show']);
        Permission::create(['name' => 'typesActivities.store']);
        Permission::create(['name' => 'typesActivities.destroy']);
        Permission::create(['name' => 'typesActivities.update']);

        Permission::create(['name' => 'activitySpeakers.index']);
        Permission::create(['name' => 'activitySpeakers.show']);
        Permission::create(['name' => 'activitySpeakers.store']);
        Permission::create(['name' => 'activitySpeakers.destroy']);
        Permission::create(['name' => 'activitySpeakers.update']);

        Permission::create(['name' => 'speakers.index']);
        Permission::create(['name' => 'speakers.show']);
        Permission::create(['name' => 'speakers.store']);
        Permission::create(['name' => 'speakers.destroy']);
        Permission::create(['name' => 'speakers.update']);

        Permission::create(['name' => 'modeActivities.index']);
        Permission::create(['name' => 'modeActivities.show']);
        Permission::create(['name' => 'modeActivities.store']);
        Permission::create(['name' => 'modeActivities.destroy']);
        Permission::create(['name' => 'modeActivities.update']);

        Permission::create(['name' => 'pollQuestions.index']);
        Permission::create(['name' => 'pollQuestions.show']);
        Permission::create(['name' => 'pollQuestions.store']);
        Permission::create(['name' => 'pollQuestions.destroy']);
        Permission::create(['name' => 'pollQuestions.update']);

        Permission::create(['name' => 'pollAnswers.index']);
        Permission::create(['name' => 'pollAnswers.show']);
        Permission::create(['name' => 'pollAnswers.store']);
        Permission::create(['name' => 'pollAnswers.destroy']);
        Permission::create(['name' => 'pollAnswers.update']);

        Permission::create(['name' => 'typeQuestions.index']);
        Permission::create(['name' => 'typeQuestions.show']);
        Permission::create(['name' => 'typeQuestions.store']);
        Permission::create(['name' => 'typeQuestions.destroy']);
        Permission::create(['name' => 'typeQuestions.update']);

        Permission::create(['name' => 'hall.index']);
        Permission::create(['name' => 'hall.show']);
        Permission::create(['name' => 'hall.store']);
        Permission::create(['name' => 'hall.destroy']);
        Permission::create(['name' => 'hall.update']);

    }
}

