<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HallTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $eventType = DB::table('hall_types')->insert([
            'name' => 'Internal',     
        ]);
        $eventType = DB::table('hall_types')->insert([
            'name' => 'External',     
        ]);
    }
}
