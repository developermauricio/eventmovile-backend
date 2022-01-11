<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'id'            => 1,
            'name'          => 'Super Administrador',
            'email'         => 'alejandro.cepeda@tars.dev',                        
            'password'      => bcrypt('010203'),
            'model_id'      => 1,
            'lastname'      => 'Cepda',
            'phone'         => '3134325874',                
        ]);

        $user->assignRole('super admin');  


        $user = User::create([
            'id'            => 2,
            'name'          => 'Admin',
            'email'         => 'cristian@tars.dev',                        
            'password'      => bcrypt('010203'),
            'model_id'      => 1,
            'lastname'      => 'Useche',
            'phone'         => '3134325874',                   
        ]);

        $user->assignRole('admin');  

        $user = User::create([
            'id'            => 3,
            'name'          => 'Invitado',
            'email'         => 'cristian.narvaez@tars.dev',                        
            'password'      => bcrypt('010203'),
            'model_id'      => 1,
            'lastname'      => 'Narvaez',
            'phone'         => '3134325874',                   
        ]);

        $user->assignRole('guest'); 
    }
}
