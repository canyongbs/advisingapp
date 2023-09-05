<?php

namespace Assist\ServiceManagement\Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;
use Assist\ServiceManagement\Models\ServiceRequestPriority;

class CreateCaseItemPriorityRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'order' => ServiceRequestPriority::orderBy('order', 'desc')->first()?->order + 1 ?? 1,
        ];
    }
}
