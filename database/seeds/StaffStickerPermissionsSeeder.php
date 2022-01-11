<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StaffStickerPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $staff = Role::findOrFail(5);
        $staff->givePermissionTo('sticker.index');
        $staff->givePermissionTo('sticker.show');
        $staff->givePermissionTo('sticker.store');
        $staff->givePermissionTo('sticker.update');
        $staff->givePermissionTo('sticker.destroy');
    }
}
