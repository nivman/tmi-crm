<?php

use Illuminate\Database\Seeder;

class EventsTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Creating Events Type";

        factory('App\EventsType')->create([
            'name'     => 'שיחה',

            'color'    => '#00b89c',
        ]);

        factory('App\EventsType')->create([
            'name'     => 'פגישה',
            'color'    => ' #1a88ff',
        ]);
        factory('App\EventsType')->create([
            'name'     => 'אימייל',
            'color'    => 'red',
        ]);
    }
}
