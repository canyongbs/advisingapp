<?php

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\Workflow\Models\WorkflowTagsDetails;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowTagsDetails>
 */
class WorkflowTagsDetailsFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tag_ids' => fn () => Tag::factory()->count(3)->create()->pluck('id')->toArray(),
            'remove_prior' => $this->faker->boolean,
        ];
    }
}
