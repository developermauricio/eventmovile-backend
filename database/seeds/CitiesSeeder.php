<?php

use Illuminate\Database\Seeder;
use App\City;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $city = City::create([
            'name' => 'Bogotá', 
            'country_id' => 1,         
        ]);
        $city = City::create([
            'name' => 'Medellin', 
            'country_id' => 1,         
        ]);
        $city = City::create([
            'name' => 'Cali', 
            'country_id' => 1,         
        ]);
    }
}
