<?php

namespace Assist\Case\Database\Factories;

use App\Models\User;
use App\Models\Institution;
use Assist\Case\Models\CaseItem;
use Assist\Case\Models\CaseItemType;
use Assist\Case\Models\CaseItemStatus;
use Assist\Case\Models\CaseItemPriority;
use Assist\AssistDataModel\Models\Student;
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
            'type_id' => CaseItemType::inRandomOrder()->first() ?? CaseItemType::factory(),
            'priority_id' => CaseItemPriority::inRandomOrder()->first() ?? CaseItemPriority::factory(),
            'assigned_to_id' => User::factory(),
            'created_by_id' => User::factory(),
        ];
    }
}
