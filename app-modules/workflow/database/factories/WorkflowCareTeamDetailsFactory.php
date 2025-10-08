<?php

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\Workflow\Models\WorkflowCareTeamDetails;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowCareTeamDetails>
 */
class WorkflowCareTeamDetailsFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'care_team',
            'remove_prior' => $this->faker->boolean(),
        ];
    }
}
