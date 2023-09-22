<?php

namespace Assist\KnowledgeBase\Tests\KnowledgeBaseItem\RequestFactories;

use App\Models\Institution;
use Worksome\RequestFactories\RequestFactory;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;

class CreateKnowledgeBaseItemRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence(),
            'public' => $this->faker->boolean(),
            'solution' => $this->faker->paragraph(),
            'notes' => $this->faker->paragraph(),
            'quality_id' => KnowledgeBaseQuality::inRandomOrder()->first()?->id ?? KnowledgeBaseQuality::factory()->create()->id,
            'status_id' => KnowledgeBaseStatus::inRandomOrder()->first()?->id ?? KnowledgeBaseStatus::factory()->create()->id,
            'category_id' => KnowledgeBaseCategory::inRandomOrder()->first()?->id ?? KnowledgeBaseCategory::factory()->create()->id,
            'institution' => [Institution::inRandomOrder()->first()?->id ?? Institution::factory()->create()->id],
        ];
    }
}
