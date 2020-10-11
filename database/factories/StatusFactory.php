<?php

use Faker\Generator as Faker;

$factory->define(App\Status::class, function (Faker $faker) {
    return [
        'name' => $faker->words(mt_rand(2, 5), true),
        'entity_name' => $faker->sentence(mt_rand(3, 6)),
        'color' => $faker->colorName
    ];
});
