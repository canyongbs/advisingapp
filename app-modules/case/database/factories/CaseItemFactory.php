<?php

namespace Assist\Case\Database\Factories;

use App\Models\User;
use Assist\AssistDataModelModule\Models\Student;
use App\Models\Institution;
use Assist\Case\Models\CaseItem;
use Assist\Case\Models\CaseItemStatus;
use Assist\Case\Models\CaseItemPriority;
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
            'state_id' => CaseItemStatus::inRandomOrder()->first() ?? CaseItemStatus::factory(),
            'type_id' => $this->faker->randomNumber(9),
            'priority_id' => CaseItemPriority::inRandomOrder()->first() ?? CaseItemPriority::factory(),
            'assigned_to_id' => User::factory(),
            'created_by_id' => $this->faker->randomNumber(9),
        ];
    }
}
