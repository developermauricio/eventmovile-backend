<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
class BusinessMarketRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['id' => 4,'name' => 'business market']);
    }
}
