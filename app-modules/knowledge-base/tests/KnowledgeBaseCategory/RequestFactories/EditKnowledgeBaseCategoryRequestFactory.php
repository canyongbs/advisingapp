<?php

namespace Assist\KnowledgeBase\Tests\KnowledgeBaseCategory\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class EditKnowledgeBaseCategoryRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
