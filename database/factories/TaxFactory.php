<?php

use Faker\Generator as Faker;

$factory->define(App\Tax::class, function (Faker $faker) {
    return [
        'rate' => mt_rand(6, 28),
        'state' => $faker->boolean,
        'same' => $faker->boolean,
        'compound' => $faker->boolean,
        'recoverable' => $faker->boolean,
        'code' => $faker->unique()->word,
        'details' => $faker->text(100, 200),
        'number' => $faker->unique()->ean8,
        'name' => $faker->words(mt_rand(1, 3), true),
    ];
});
