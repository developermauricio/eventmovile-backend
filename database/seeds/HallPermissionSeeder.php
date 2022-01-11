<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class HallPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'halls.index']);
        Permission::create(['name' => 'halls.show']);
        Permission::create(['name' => 'halls.store']);
        Permission::create(['name' => 'halls.update']);
        Permission::create(['name' => 'halls.destroy']);
    }
}
