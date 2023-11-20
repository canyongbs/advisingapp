<?php

namespace Assist\Prospect\Tests\ProspectStatus\RequestFactories;

use Worksome\RequestFactories\RequestFactory;
use Assist\Prospect\Enums\ProspectStatusColorOptions;
use Assist\Prospect\Enums\SystemProspectClassification;

class EditProspectStatusRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'classification' => fake()->randomElement(SystemProspectClassification::cases()),
            'name' => fake()->name(),
            'color' => fake()->randomElement(ProspectStatusColorOptions::cases()),
        ];
    }
}
