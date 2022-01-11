<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class BM_rel_users_permisions_asign extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $bm = Role::findOrFail(4);
        $bm->givePermissionTo('business-market-rel-user.index');
        $bm->givePermissionTo('business-market-rel-user.show');
        $bm->givePermissionTo('business-market-rel-user.store');
        $bm->givePermissionTo('business-market-rel-user.update');
        $bm->givePermissionTo('business-market-rel-user.destroy');
    }
}
