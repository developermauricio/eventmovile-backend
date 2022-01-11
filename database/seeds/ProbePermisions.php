<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ProbePermisions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'probe.index']);
        Permission::create(['name' => 'probe.show']);
        Permission::create(['name' => 'probe.store']);
        Permission::create(['name' => 'probe.update']);
        Permission::create(['name' => 'probe.destroy']);

        Permission::create(['name' => 'probe-questions.index']);
        Permission::create(['name' => 'probe-questions.show']);
        Permission::create(['name' => 'probe-questions.store']);
        Permission::create(['name' => 'probe-questions.update']);
        Permission::create(['name' => 'probe-questions.destroy']);

        Permission::create(['name' => 'probe-answers.index']);
        Permission::create(['name' => 'probe-answers.show']);
        Permission::create(['name' => 'probe-answers.store']);
        Permission::create(['name' => 'probe-answers.update']);
        Permission::create(['name' => 'probe-answers.destroy']);
    }
}
