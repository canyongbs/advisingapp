<?php

namespace Assist\KnowledgeBase\Tests\KnowledgeBaseStatus\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class CreateKnowledgeBaseStatusRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
