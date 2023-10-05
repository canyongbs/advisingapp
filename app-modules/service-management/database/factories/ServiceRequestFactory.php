<?php

namespace Assist\ServiceManagement\Database\Factories;

use App\Models\User;
use Assist\Division\Models\Division;
use Assist\AssistDataModel\Models\Student;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Models\ServiceRequestPriority;

/**
 * @extends Factory<ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'respondent_id' => Student::inRandomOrder()->first()->sisid ?? Student::factory(),
            'respondent_type' => function (array $attributes) {
                return Student::find($attributes['respondent_id'])->getMorphClass();
            },
            'close_details' => $this->faker->sentence(),
            'res_details' => $this->faker->sentence(),
            'division_id' => Division::factory(),
            'status_id' => ServiceRequestStatus::inRandomOrder()->first() ?? ServiceRequestStatus::factory(),
            'type_id' => ServiceRequestType::inRandomOrder()->first() ?? ServiceRequestType::factory(),
            'priority_id' => ServiceRequestPriority::inRandomOrder()->first() ?? ServiceRequestPriority::factory(),
            'assigned_to_id' => User::factory(),
            'created_by_id' => User::factory(),
        ];
    }
}
