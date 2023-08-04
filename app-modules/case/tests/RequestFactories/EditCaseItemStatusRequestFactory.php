<?php

namespace Assist\Case\Tests\RequestFactories;

use Assist\Case\Enums\ColumnColorOptions;
use Worksome\RequestFactories\RequestFactory;

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
