<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class TicketPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'tickets.index']);
        Permission::create(['name' => 'tickets.show']);
        Permission::create(['name' => 'tickets.store']);
        Permission::create(['name' => 'tickets.update']);
        Permission::create(['name' => 'tickets.destroy']);
    }
}
