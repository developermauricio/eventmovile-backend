<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class StickerPermissions extends Seeder
{
    
    public function run()
    {
        Permission::create(['name' => 'sticker.index']);
        Permission::create(['name' => 'sticker.show']);
        Permission::create(['name' => 'sticker.store']);
        Permission::create(['name' => 'sticker.update']);
        Permission::create(['name' => 'sticker.destroy']);
    }
}
