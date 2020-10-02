<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(App\Recurring::class, function (Faker $faker) {
    $repeat = ['daily', 'weekly', 'monthly', 'quarterly', 'semiannually', 'yearly', 'biennially'];

    return [
        'active'        => true,
        'discount'      => null,
        'shipping'      => null,
        'reference'     => Str::random(),
        'note'          => $faker->paragraph,
        'start_date'    => date('Y-m-d'),
        'total'         => $faker->numberBetween(1, 50),
        'user_id'       => $faker->numberBetween(1, 3),
        'repeat'        => $faker->randomElement($repeat),
        'customer_id'   => $faker->numberBetween(1, 30),
        'grand_total'   => $faker->numberBetween(1, 50),
        'create_before' => $faker->numberBetween(1, 3),
    ];
});

$factory->define(App\RecurringItem::class, function (Faker $faker) {
    return [
        'code'       => $faker->unique()->ean13,
        'name'       => $faker->words(mt_rand(2, 5), true),
        'qty'        => $qty = $faker->numberBetween(1, 5),
        'price'      => $price = $faker->numberBetween(10, 50),
        'unit_price' => $price,
        'subtotal'   => ($price * $qty),
    ];
});
