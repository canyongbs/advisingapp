<?php

use Faker\Generator as Faker;
use Assist\LaravelAuditing\Models\Audit;
use Assist\LaravelAuditing\Tests\Models\User;
use Assist\LaravelAuditing\Tests\Models\Article;

/*
|--------------------------------------------------------------------------
| Audit Factories
|--------------------------------------------------------------------------
|
*/

($factory ?? null)?->define(Audit::class, function (Faker $faker) {
    $morphPrefix = Config::get('audit.user.morph_prefix', 'user');

    return [
        $morphPrefix . '_id' => function () {
            return factory(User::class)->create()->id;
        },
        $morphPrefix . '_type' => User::class,
        'event' => 'updated',
        'auditable_id' => function () {
            return factory(Article::class)->create()->id;
        },
        'auditable_type' => Article::class,
        'old_values' => [],
        'new_values' => [],
        'url' => $faker->url,
        'ip_address' => $faker->ipv4,
        'user_agent' => $faker->userAgent,
        'tags' => implode(',', $faker->words(4)),
    ];
});
