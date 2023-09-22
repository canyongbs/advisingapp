<?php

namespace Assist\KnowledgeBase\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;

/**
 * @extends Factory<KnowledgeBaseStatus>
 */
class KnowledgeBaseStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
