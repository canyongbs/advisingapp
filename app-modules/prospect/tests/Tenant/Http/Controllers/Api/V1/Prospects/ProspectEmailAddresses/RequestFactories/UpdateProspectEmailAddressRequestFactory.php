<?php

namespace AdvisingApp\Prospect\Tests\Tenant\Http\Controllers\Api\V1\Prospects\ProspectEmailAddresses\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class UpdateProspectEmailAddressRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return array_filter([
            'address' => $this->faker->optional()->safeEmail(),
            'type' => $this->faker->optional()->word(),
            'order' => $this->faker->optional()->numberBetween(1, 10),
        ], filled(...));
    }
}
