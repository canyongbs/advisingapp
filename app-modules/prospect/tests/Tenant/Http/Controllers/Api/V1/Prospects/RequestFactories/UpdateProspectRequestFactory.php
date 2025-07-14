<?php

namespace AdvisingApp\Prospect\Tests\Tenant\Http\Controllers\Api\V1\Prospects\RequestFactories;

use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use Worksome\RequestFactories\RequestFactory;

class UpdateProspectRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return array_filter([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => "{$firstName} {$lastName}",
            'preferred' => $this->faker->optional()->firstName(),
            'description' => $this->faker->paragraph(),
            'sms_opt_out' => $this->faker->optional()->randomElement([true, false, null]),
            'email_bounce' => $this->faker->optional()->randomElement([true, false, null]),
            'status' => ProspectStatus::inRandomOrder()->first()->name ?? ProspectStatus::factory()->create()->name,
            'source' => ProspectSource::inRandomOrder()->first()->name ?? ProspectSource::factory()->create()->name,
            'birthdate' => $this->faker->optional()->randomElement([$this->faker->date('Y-m-d'), null]),
            'hsgrad' => $this->faker->optional()->randomElement([$this->faker->numberBetween(1980, 2030), null]),
        ], filled(...));
    }
}
