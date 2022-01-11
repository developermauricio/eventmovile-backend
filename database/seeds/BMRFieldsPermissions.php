<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class BMRFieldsPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'bm-register-fields.index']);
        Permission::create(['name' => 'bm-register-fields.show']);
        Permission::create(['name' => 'bm-register-fields.store']);
        Permission::create(['name' => 'bm-register-fields.update']);
        Permission::create(['name' => 'bm-register-fields.destroy']);

        Permission::create(['name' => 'bm-register-fields-data.index']);
        Permission::create(['name' => 'bm-register-fields-data.show']);
        Permission::create(['name' => 'bm-register-fields-data.store']);
        Permission::create(['name' => 'bm-register-fields-data.update']);
        Permission::create(['name' => 'bm-register-fields-data.destroy']);
    }
}
