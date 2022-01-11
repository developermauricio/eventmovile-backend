<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RegisterEventPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'registerEvent.index']);
        Permission::create(['name' => 'registerEvent.show']);
        Permission::create(['name' => 'registerEvent.store']);
        Permission::create(['name' => 'registerEvent.update']);
        Permission::create(['name' => 'registerEvent.destroy']);
    }
}
