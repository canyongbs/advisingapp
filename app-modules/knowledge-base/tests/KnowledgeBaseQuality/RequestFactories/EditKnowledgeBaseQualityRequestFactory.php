<?php

namespace Assist\KnowledgeBase\Tests\KnowledgeBaseQuality\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class EditKnowledgeBaseQualityRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
