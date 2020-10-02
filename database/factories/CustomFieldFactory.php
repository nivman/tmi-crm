<?php

use Faker\Generator as Faker;

$factory->define(App\CustomField::class, function (Faker $faker) {
    $n = $faker->unique()->randomDigitNotNull;
    $types = ['varchar', 'text', 'boolean', 'datetime', 'integer'];
    $entitiesOpts = ["App\Account", "App\Customer", "App\Expense", "App\Invoice", "App\Seller", "App\Product"];
    $entities = $faker->randomElements($entitiesOpts, mt_rand(3, 5));
    return [
        'slug' => 'cf'.$n,
        'is_required' => false,
        'entities' => $entities,
        'name' => 'Custom Field '.$n,
        'type' => $types[mt_rand(0, 4)],
        'description' => 'Custom field '.$n.' description',
    ];
});
