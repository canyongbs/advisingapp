<?php

namespace Assist\CaseModule\Database\Factories;

use App\Models\User;
use App\Models\Student;
use App\Models\Institution;
use Assist\CaseModule\Models\CaseItem;
use Assist\CaseModule\Models\CaseItemStatus;
use Assist\CaseModule\Models\CaseItemPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseItem>
 */
class CaseItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'casenumber' => $this->faker->randomNumber(9),
            'respondent_id' => Student::factory(),
            'respondent_type' => function (array $attributes) {
                return Student::find($attributes['respondent_id'])->getMorphClass();
            },
            'close_details' => $this->faker->sentence(),
            'res_details' => $this->faker->sentence(),
            'institution_id' => Institution::factory(),
            'state_id' => CaseItemStatus::factory(),
            'type_id' => $this->faker->randomNumber(9),
            'priority_id' => CaseItemPriority::factory(),
            'assigned_to_id' => User::factory(),
            'created_by_id' => $this->faker->randomNumber(9),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (CaseItem $case) {
        })->afterCreating(function (CaseItem $case) {
            $this->generatePriority($case);
            $this->generateStatus($case);
        });
    }

    protected function generatePriority(CaseItem $case): void
    {
        $priority = CaseItemPriority::inRandomOrder()->first();

        if (! $priority) {
            $priority = CaseItemPriority::factory()->high()->create();
        }

        $case->priority()->associate($priority)->save();
    }

    protected function generateStatus(CaseItem $case): void
    {
        $priority = CaseItemStatus::inRandomOrder()->first();

        if (! $priority) {
            $priority = CaseItemStatus::factory()->open()->create();
        }

        $case->state()->associate($priority)->save();
    }
}
