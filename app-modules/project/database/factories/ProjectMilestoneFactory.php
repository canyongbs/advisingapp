<?php

namespace AdvisingApp\Project\Database\Factories;

use AdvisingApp\Project\Models\Project;
use AdvisingApp\Project\Models\ProjectMilestone;
use AdvisingApp\Project\Models\ProjectMilestoneStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectMilestone>
 */
class ProjectMilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => str($this->faker->words(asText: true))->headline()->toString(),
            'description' => $this->faker->sentence(3),
            'status_id' => ProjectMilestoneStatus::factory(),
            'created_by_id' => User::factory(),
        ];
    }
}
