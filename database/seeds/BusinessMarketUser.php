<?php

use Illuminate\Database\Seeder;
use App\User;

class BusinessMarketUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            
            'name'          => 'Javier',
            'email'         => 'jose.saldana@tars.dev',                        
            'password'      => bcrypt('010203'),
            'model_id'      => 1,
            'lastname'      => 'Saldaña',
            'phone'         => '3184989906',                
        ]);

        $user->assignRole('business market');  
    }
}
