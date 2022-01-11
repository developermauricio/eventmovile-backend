<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class HabeasDataPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'habeasData.index']);
        Permission::create(['name' => 'habeasData.show']);
        Permission::create(['name' => 'habeasData.store']);
        Permission::create(['name' => 'habeasData.update']);
        Permission::create(['name' => 'habeasData.destroy']);
    }
}
