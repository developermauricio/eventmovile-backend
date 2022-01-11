<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DataRegisterPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'dataRegisters.index']);
        Permission::create(['name' => 'dataRegisters.show']);
        Permission::create(['name' => 'dataRegisters.store']);
        Permission::create(['name' => 'dataRegisters.update']);
        Permission::create(['name' => 'dataRegisters.destroy']);
    }
}
