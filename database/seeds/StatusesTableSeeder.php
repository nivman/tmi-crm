<?php

use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Creating Statuses";

        factory('App\Status')->create([
            'name'     => 'סטטוס 1',
            'entity_name' => 'App\Customer',
            'color'    => '#00b89c',
        ]);
        factory('App\Status')->create([
            'name'     => 'סטטוס 2',
            'entity_name' => 'App\Customer',
            'color'    => ' #1a88ff',
        ]);
    }
}
