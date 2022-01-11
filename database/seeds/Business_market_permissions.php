<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class Business_market_permissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'product.index']);
        Permission::create(['name' => 'product.show']);
        Permission::create(['name' => 'product.store']);
        Permission::create(['name' => 'product.update']);
        Permission::create(['name' => 'product.destroy']);
        Permission::create(['name' => 'products-by-id.show']);

        Permission::create(['name' => 'business-market.index']);
        Permission::create(['name' => 'business-market.show']);
        Permission::create(['name' => 'business-market.store']);
        Permission::create(['name' => 'business-market.update']);
        Permission::create(['name' => 'business-market.destroy']);
    
        Permission::create(['name' => 'business-market-user.index']);
        Permission::create(['name' => 'business-market-user.show']);
        Permission::create(['name' => 'business-market-user.store']);
        Permission::create(['name' => 'business-market-user.update']);
        Permission::create(['name' => 'business-market-user.destroy']);
        Permission::create(['name' => 'business-market-user-by-id.show']);

    }
}
