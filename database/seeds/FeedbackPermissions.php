<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class FeedbackPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'feedback-question.index']);
        Permission::create(['name' => 'feedback-answer.store']);
        Permission::create(['name' => 'feedback-report.show']);
    }
}
