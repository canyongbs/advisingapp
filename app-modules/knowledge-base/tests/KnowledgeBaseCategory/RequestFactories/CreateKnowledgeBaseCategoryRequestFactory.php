<?php

namespace Assist\KnowledgeBase\Tests\KnowledgeBaseCategory\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class CreateKnowledgeBaseCategoryRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
