<?php

use Faker\Generator as Faker;
use Assist\Auditing\Tests\Models\User;

/*
|--------------------------------------------------------------------------
| User Factories
|--------------------------------------------------------------------------
|
*/

($factory ?? null)?->define(User::class, function (Faker $faker) {
    return [
        'is_admin' => $faker->randomElement([0, 1]),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
    ];
});
