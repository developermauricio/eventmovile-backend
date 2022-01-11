<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TicketAssignPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guest = Role::findOrFail(3);
        $guest->givePermissionTo('tickets.index');
        $guest->givePermissionTo('tickets.show');

        $admin = Role::findOrFail(2);
        $admin->givePermissionTo('tickets.index');
        $admin->givePermissionTo('tickets.show');
        $admin->givePermissionTo('tickets.store');
        $admin->givePermissionTo('tickets.update');
        $admin->givePermissionTo('tickets.destroy');
    }
}
