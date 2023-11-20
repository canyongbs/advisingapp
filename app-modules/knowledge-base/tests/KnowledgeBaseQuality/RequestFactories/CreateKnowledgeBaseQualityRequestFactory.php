<?php

namespace Assist\KnowledgeBase\Tests\KnowledgeBaseQuality\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class CreateKnowledgeBaseQualityRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
