<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProbePermisionsAsign extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::findOrFail(2);
        $admin->givePermissionTo(Permission::all());

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo(Permission::all());

        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('probe-questions.index');
        $guest->givePermissionTo('probe-questions.show');

        $guest->givePermissionTo('probe-answers.index');
        $guest->givePermissionTo('probe-answers.show');
        $guest->givePermissionTo('probe-answers.store');

    }
}
