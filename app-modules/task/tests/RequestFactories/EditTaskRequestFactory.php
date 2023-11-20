<?php

namespace Assist\Task\Tests\RequestFactories;

use App\Models\User;
use Assist\Task\Enums\TaskStatus;
use Assist\AssistDataModel\Models\Student;
use Worksome\RequestFactories\RequestFactory;

class EditTaskRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        $student = Student::factory()->create();

        return [
            'title' => str(fake()->words(asText: 3))->title()->toString(),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(TaskStatus::cases())->value,
            'due' => now()->addWeek(),
            'assigned_to' => User::factory()->create()->id,
            'concern_id' => $student->id,
            'concern_type' => $student->getMorphClass(),
        ];
    }
}
