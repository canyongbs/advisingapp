<?php

namespace Assist\KnowledgeBase\Database\Factories;

use App\Models\Institution;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;

/**
 * @extends Factory<KnowledgeBaseItem>
 */
class KnowledgeBaseItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence(),
            'public' => $this->faker->boolean(),
            'solution' => $this->faker->paragraph(),
            'notes' => $this->faker->paragraph(),
            'quality_id' => KnowledgeBaseQuality::inRandomOrder()->first() ?? KnowledgeBaseQuality::factory(),
            'status_id' => KnowledgeBaseStatus::inRandomOrder()->first() ?? KnowledgeBaseStatus::factory(),
            'category_id' => KnowledgeBaseCategory::inRandomOrder()->first() ?? KnowledgeBaseCategory::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (KnowledgeBaseItem $knowledgeBaseItem) {
            // ...
        })->afterCreating(function (KnowledgeBaseItem $knowledgeBaseItem) {
            if ($knowledgeBaseItem->institution->isEmpty()) {
                $knowledgeBaseItem->institution()->attach(Institution::first()?->id ?? Institution::factory()->create()->id);
                $knowledgeBaseItem->save();
            }
        });
    }
}
