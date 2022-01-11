<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class Permissions_business_market_user extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $bm = Role::findOrFail(4);
        $bm->givePermissionTo('product.index');
        $bm->givePermissionTo('product.show');
        $bm->givePermissionTo('product.store');
        $bm->givePermissionTo('product.update');
        $bm->givePermissionTo('product.destroy');
        $bm->givePermissionTo('products-by-id.show');

        $bm->givePermissionTo('business-market.index');
        $bm->givePermissionTo('business-market.show');
        $bm->givePermissionTo('business-market.store');
        $bm->givePermissionTo('business-market.update');
        $bm->givePermissionTo('business-market.destroy');
        
        $bm->givePermissionTo('business-market-user.index');
        $bm->givePermissionTo('business-market-user.show');
        $bm->givePermissionTo('business-market-user.store');
        $bm->givePermissionTo('business-market-user.update');
        $bm->givePermissionTo('business-market-user.destroy');
        $bm->givePermissionTo('business-market-user-by-id.show');

        
    }
}
