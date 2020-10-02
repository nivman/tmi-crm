<?php

use Faker\Generator as Faker;

$factory->define(App\Payment::class, function (Faker $faker) {
    return [
        'received'   => true,
        'gateway'    => 'cash',
        'user_id'    => auth()->id(),
        'note'       => $faker->paragraph,
        'amount'     => mt_rand(10, 99),
        'account_id' => mt_rand(1, 5),
        'reference'  => \Ulid\Ulid::generate(true),
    ];
});
