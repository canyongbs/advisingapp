<?php

namespace Assist\Case\Database\Factories;

use App\Models\User;
use App\Models\Institution;
use Assist\Case\Models\ServiceRequest;
use Assist\Case\Models\CaseItemPriority;
use Assist\Case\Models\ServiceRequestType;
use Assist\AssistDataModel\Models\Student;
use Assist\Case\Models\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequest>
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
            'status_id' => ServiceRequestStatus::inRandomOrder()->first() ?? ServiceRequestStatus::factory(),
            'type_id' => ServiceRequestType::inRandomOrder()->first() ?? ServiceRequestType::factory(),
            'priority_id' => CaseItemPriority::inRandomOrder()->first() ?? CaseItemPriority::factory(),
            'assigned_to_id' => User::factory(),
            'created_by_id' => User::factory(),
        ];
    }
}
