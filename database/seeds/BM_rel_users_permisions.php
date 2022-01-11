<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class BM_rel_users_permisions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'business-market-rel-user.index']);
        Permission::create(['name' => 'business-market-rel-user.show']);
        Permission::create(['name' => 'business-market-rel-user.store']);
        Permission::create(['name' => 'business-market-rel-user.update']);
        Permission::create(['name' => 'business-market-rel-user.destroy']);
    }
}
