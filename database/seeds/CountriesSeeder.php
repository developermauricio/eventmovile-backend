<?php

use Illuminate\Database\Seeder;

use App\Country;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $country = Country::create([
            'name'          => 'Colombia',          
        ]);
    }
}
