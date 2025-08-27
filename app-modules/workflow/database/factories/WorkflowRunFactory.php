<?php

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowRun>
 */
class WorkflowRunFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workflow_trigger_id' => WorkflowTrigger::factory(),
            'related_type' => (new Student())->getMorphClass(),
            'related_id' => Student::factory(),
            'started_at' => now(),
        ];
    }

    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => null,
        ]);
    }
}
