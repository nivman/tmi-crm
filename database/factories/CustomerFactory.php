<?php

use Faker\Generator as Faker;

$factory->define(App\Customer::class, function (Faker $faker) {
    $earth = new \MenaraSolutions\Geographer\Earth();
    $state = collect($earth->findOneByCode('IN')->getStates()->toArray())->transform(function ($item) {
        return ['value' => $item['isoCode'], 'label' => $item['name']];
    })->random();

    return [
        'country' => 'IN',
        'name' => $faker->name,
        'email' => $faker->email,
        'country_name' => 'India',
        'state' => $state['value'],
        'company' => $faker->company,
        'state_name' => $state['label'],
        'phone' => $faker->phoneNumber,
        'address' => $faker->streetAddress,
        'user_id' => $faker->numberBetween(1, 3),
    ];
});
