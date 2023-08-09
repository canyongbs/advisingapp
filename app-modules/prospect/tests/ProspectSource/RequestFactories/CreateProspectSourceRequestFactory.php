<?php

namespace Assist\Prospect\Tests\ProspectSource\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class CreateProspectSourceRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
