<?php

namespace AdvisingApp\Task\Database\Factories;

use AdvisingApp\Project\Models\Project;
use AdvisingApp\Task\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\Task\Models\ConfidentialTasksProjects>
 */
class ConfidentialTasksProjectsFactory extends Factory
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
            'project_id' => Project::factory(),
        ];
    }
}
