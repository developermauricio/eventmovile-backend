<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $eventType = DB::table('event_types')->insert([
            'name' => 'Virtual',     
        ]);
        $eventType = DB::table('event_types')->insert([
            'name' => 'Presencial',     
        ]);
        $eventType = DB::table('event_types')->insert([
            'name' => 'Híbrido',     
        ]);
    }
}
