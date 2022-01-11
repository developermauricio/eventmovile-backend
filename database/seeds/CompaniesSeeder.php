<?php

use Illuminate\Database\Seeder;
use App\Company;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $company = Company::create([
            'id' => 1,
            'name' => 'TARS',
            'sort_description' => 'startup',
            'email' => 'tars@tars.dev',
            'phone' => 3213213213,
            'pic'=> 'https://6e67', 
            'city_id' => 1, 
            'country_id' => 1,
            'address' => 'Frente al bar',
            'location_coordinates'  => '43521321321,54654623'       
        ]);
    }
}
