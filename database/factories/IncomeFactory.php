<?php

use Faker\Generator as Faker;

$factory->define(App\Income::class, function (Faker $faker) {
    return [
        'user_id'    => 1,
        'title'      => $faker->name,
        'details'    => $faker->text(100, 200),
        'user_id'    => $faker->numberBetween(1, 3),
        'reference'  => \Ulid\Ulid::generate(true),
        'amount'     => $faker->numberBetween(10, 100),
        'account_id' => $faker->numberBetween(1, 5),
    ];
});
