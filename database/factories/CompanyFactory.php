<?php

use Faker\Generator as Faker;

$factory->define(App\Company::class, function (Faker $faker) {
    return [
        'country' => 'IN',
        'state' => 'IN-DL',
        'state_name' => 'Delhi',
        'country_name' => 'India',
        'number' => $faker->isbn10,
        'email' => $faker->safeEmail,
        'footer' => $faker->text(160),
        'logo' => 'images/default.png',
        'address' => $faker->streetAddress,
        'phone' => $faker->e164PhoneNumber,
        'name' => $faker->company . ' ' . $faker->companySuffix,
        'extra' => json_encode(['cf1_label' => 'Tax No.', 'cf1_value' => '98989898', 'cf2_label' => '2nd field', 'cf2_value' => 'Value of 2nd field']),
    ];
});
