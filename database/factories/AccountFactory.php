<?php

use Faker\Generator as Faker;

$factory->define(App\Account::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'type' => $faker->word,
        'reference' => $faker->word,
        'offline' => $faker->boolean,
        'details' => $faker->text(100, 200),
        'opening_balance' => $faker->randomNumber(),
    ];
});
