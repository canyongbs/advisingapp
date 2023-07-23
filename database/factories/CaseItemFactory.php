<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\CaseItem;
use App\Models\CaseItemPriority;
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
            'respondent_type' => 'App\Models\Student',
            'respondent_id' => Student::factory(),
            'close_details' => $this->faker->sentence(),
            'res_details' => $this->faker->sentence(),
            'institution_id' => $this->faker->randomNumber(9),
            'state_id' => $this->faker->randomNumber(9),
            'type_id' => $this->faker->randomNumber(9),
            'priority_id' => $this->faker->randomNumber(9),
            'assigned_to_id' => $this->faker->randomNumber(9),
            'created_by_id' => $this->faker->randomNumber(9),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (CaseItem $case) {
        })->afterCreating(function (CaseItem $case) {
            $this->generatePriority($case);
        });
    }

    protected function generatePriority(CaseItem $case): void
    {
        $priority = CaseItemPriority::inRandomOrder()->first();

        if (! $priority) {
            $priority = CaseItemPriority::factory()->state('high')->create();
        }

        $case->priority()->associate($priority)->save();
    }
}
