<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
            'title' => str(fake()->words(asText: 3))->title()->toString(),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(TaskStatus::cases())->value,
            'due' => null,
            'assigned_to' => null,
            'created_by' => User::factory(),
            'concern_id' => null,
            'concern_type' => null,
        ];
    }

    public function concerningStudent(Student $student = null): self
    {
        return $this->state([
            'concern_id' => $student?->id ?? fn () => Student::inRandomOrder()->first()->sisid ?? Student::factory(),
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
            'due' => fake()->dateTimeBetween('-2 weeks', '-1 week'),
        ]);
    }

    public function dueLater(): self
    {
        return $this->state([
            'due' => fake()->dateTimeBetween('+1 week', '+2 weeks'),
        ]);
    }
}
