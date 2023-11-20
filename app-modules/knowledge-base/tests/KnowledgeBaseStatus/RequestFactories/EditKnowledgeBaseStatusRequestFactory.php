<?php

namespace Assist\KnowledgeBase\Tests\KnowledgeBaseStatus\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class EditKnowledgeBaseStatusRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
