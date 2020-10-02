<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Product::class, function (Faker $faker) {
    $cost = $faker->randomNumber(2);
    $price = $cost * 1.4;
    return [
        'cost' => $cost,
        'price' => $price,
        'code' => $faker->unique()->ean13,
        'details' => $faker->text(100, 200),
        'name' => $faker->words(mt_rand(2, 5), true),
    ];
});
