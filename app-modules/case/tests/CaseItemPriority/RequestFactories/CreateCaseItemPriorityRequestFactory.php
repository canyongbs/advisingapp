<?php

namespace CaseItemPriority\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class CreateCaseItemPriorityRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'order' => $this->faker->randomNumber(1),
        ];
    }
}
