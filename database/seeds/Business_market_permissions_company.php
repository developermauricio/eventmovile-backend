<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class Business_market_permissions_company extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bm = Role::findOrFail(4);
        $bm->givePermissionTo('company.index');
        $bm->givePermissionTo('company.show');
        $bm->givePermissionTo('company.store');
        $bm->givePermissionTo('company.update');
        $bm->givePermissionTo('company.destroy');
    }
}
