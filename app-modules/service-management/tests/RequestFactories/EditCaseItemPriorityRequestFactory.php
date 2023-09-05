<?php

namespace Assist\ServiceManagement\Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class EditCaseItemPriorityRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
