<?php

namespace Assist\Task\Database\Factories;

use App\Models\User;
use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence,
            'status' => $this->faker->randomElement(TaskStatus::cases())->value,
            'due' => null,
            'assigned_to' => null,
            'concern_id' => null,
            'concern_type' => null,
        ];
    }

    public function concerningStudent(Student $student = null): self
    {
        return $this->state([
            'concern_id' => $student?->id ?? Student::factory(),
            'concern_type' => (new Student())->getMorphClass(),
        ]);
    }

    public function concerningProspect(Prospect $prospect = null): self
    {
        return $this->state([
            'concern_id' => $prospect?->id ?? Prospect::factory(),
            'concern_type' => (new Prospect())->getMorphClass(),
        ]);
    }

    public function assigned(User $user = null): self
    {
        return $this->state([
            'assigned_to' => $user?->id ?? User::factory(),
        ]);
    }

    public function pastDue(): self
    {
        return $this->state([
            'due' => $this->faker->dateTimeBetween('-2 weeks', '-1 week'),
        ]);
    }

    public function dueLater(): self
    {
        return $this->state([
            'due' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
        ]);
    }
}
