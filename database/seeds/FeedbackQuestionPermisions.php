<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class FeedbackQuestionPermisions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'feedback-question.store']);
        Permission::create(['name' => 'feedback-question.update']);
        Permission::create(['name' => 'feedback-question.show']);
        Permission::create(['name' => 'feedback-question.destroy']);
    }
}
