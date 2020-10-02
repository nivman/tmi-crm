<?php

use Faker\Generator as Faker;

$factory->define(App\Purchase::class, function (Faker $faker) {
    return [
        'date'        => $faker->dateTimeBetween('-3 months'),
        'draft'       => $faker->boolean,
        'total'       => $faker->numberBetween(1, 50),
        'user_id'     => $faker->numberBetween(1, 3),
        'vendor_id'   => $faker->numberBetween(1, 30),
        'grand_total' => $faker->numberBetween(1, 50),
        'reference'   => \Ulid\Ulid::generate(true),
        'note'        => $faker->paragraph,
        'discount'    => null,
        'shipping'    => null,
    ];
});

$factory->define(App\PurchaseItem::class, function (Faker $faker) {
    return [
        'code'      => $faker->unique()->ean13,
        'name'      => $faker->words(mt_rand(2, 5), true),
        'qty'       => $qty = $faker->numberBetween(1, 5),
        'cost'      => $cost = $faker->numberBetween(10, 50),
        'unit_cost' => $cost,
        'subtotal'  => ($cost * $qty),
    ];
});
