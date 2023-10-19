<?php

namespace Assist\ServiceManagement\Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;
use Assist\ServiceManagement\Enums\ColumnColorOptions;
use Assist\ServiceManagement\Enums\SystemServiceRequestClassification;

class CreateServiceRequestStatusRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'classification' => fake()->randomElement(SystemServiceRequestClassification::cases()),
            'name' => fake()->name(),
            'color' => fake()->randomElement(ColumnColorOptions::cases()),
        ];
    }
}
