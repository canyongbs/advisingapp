<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Article Factories
|--------------------------------------------------------------------------
|
*/

($factory ?? null)?->define(\Assist\LaravelAuditing\Tests\Models\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->colorName(),
    ];
});
