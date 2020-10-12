<?php

use Faker\Generator as Faker;

$factory->define(App\EventsType::class, function (Faker $faker) {

    return [
        'name' => $faker->words(mt_rand(2, 5), true),
        'color' => $faker->colorName
    ];
});
