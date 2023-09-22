<?php

namespace Assist\KnowledgeBase\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;

/**
 * @extends Factory<KnowledgeBaseQuality>
 */
class KnowledgeBaseQualityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
