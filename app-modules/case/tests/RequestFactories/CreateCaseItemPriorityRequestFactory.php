<?php

namespace Assist\Case\Tests\RequestFactories;

use Assist\Case\Models\CaseItemPriority;
use Worksome\RequestFactories\RequestFactory;

class CreateCaseItemPriorityRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'order' => CaseItemPriority::orderBy('order', 'desc')->first()?->order + 1 ?? 1,
        ];
    }
}
