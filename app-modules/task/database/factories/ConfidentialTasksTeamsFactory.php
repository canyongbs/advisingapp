<?php

namespace AdvisingApp\Task\Database\Factories;

use AdvisingApp\Task\Models\Task;
use AdvisingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\Task\Models\ConfidentialTasksTeams>
 */
class ConfidentialTasksTeamsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'team_id' => Team::factory(),
        ];
    }
}
