<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FeedbackPermissionsAsign extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super = Role::findOrFail(1);
        $super->givePermissionTo(Permission::all());

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo(Permission::all());

        $bm = Role::findOrFail(4);
        $bm->givePermissionTo('feedback-question.index');
        $bm->givePermissionTo('feedback-answer.store');
    }
}
