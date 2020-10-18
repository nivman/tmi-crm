<?php

use Faker\Generator as Faker;

$factory->define(App\TaskPriority::class, function (Faker $faker) {
    return [
        'name' => $faker->words(mt_rand(2, 5), true),
        'color' => $faker->colorName
    ];
});
