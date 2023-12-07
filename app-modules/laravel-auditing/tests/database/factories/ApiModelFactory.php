<?php

use Ramsey\Uuid\Uuid;
use Faker\Generator as Faker;
use Assist\LaravelAuditing\Tests\Models\ApiModel;

/*
|--------------------------------------------------------------------------
| APIModel Factories
|--------------------------------------------------------------------------
|
*/

($factory ?? null)?->define(ApiModel::class, function (Faker $faker) {
    return [
        'api_model_id' => Uuid::uuid4(),
        'content' => $faker->unique()->paragraph(6),
        'published_at' => null,
    ];
});
