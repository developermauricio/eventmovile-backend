<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['id' => 1,'name' => 'super admin']);
        Role::create(['id' => 2,'name' => 'admin']);
        Role::create(['id' => 3,'name' => 'guest']);
    }
}
