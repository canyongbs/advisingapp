<?php

namespace Assist\ServiceManagement\Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;
use Assist\ServiceManagement\Enums\ColumnColorOptions;

class EditCaseItemStatusRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'color' => $this->faker->randomElement(ColumnColorOptions::cases())->value,
        ];
    }
}
