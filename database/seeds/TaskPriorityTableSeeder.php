<?php

use Illuminate\Database\Seeder;

class TaskPriorityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Creating Task Priority Type";

        factory('App\TaskPriority')->create([
            'name'     => 'נמוך',

            'color'    => '#00b89c',
        ]);

        factory('App\TaskPriority')->create([
            'name'     => 'בינוני',
            'color'    => ' #1a88ff',
        ]);
        factory('App\TaskPriority')->create([
            'name'     => 'דחוף',
            'color'    => 'red',
        ]);
    }
}
