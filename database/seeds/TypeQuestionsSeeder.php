<?php

use Illuminate\Database\Seeder;

use App\TypeQuestion;

class TypeQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = TypeQuestion::create([
            'name' => 'Texto corto',          
        ]);
        $type = TypeQuestion::create([
            'name' => 'Númerico',          
        ]);
        $type = TypeQuestion::create([
            'name' => 'Menu desplegable',          
        ]);

    }
}
