<?php

namespace AdvisingApp\Project\Database\Factories;

use AdvisingApp\Project\Models\ProjectMilestoneStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectMilestoneStatus>
 */
class ProjectMilestoneStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
        ];
    }
}
