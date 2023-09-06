<?php

namespace Assist\ServiceManagement\Database\Factories;

use App\Models\User;
use App\Models\Institution;
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
            // TODO: When we decide on how to generate these numbers, we can update this.
            'service_request_number' => $this->faker->randomNumber(9),
            'respondent_id' => Student::factory(),
            'respondent_type' => function (array $attributes) {
                return Student::find($attributes['respondent_id'])->getMorphClass();
            },
            'close_details' => $this->faker->sentence(),
            'res_details' => $this->faker->sentence(),
            'institution_id' => Institution::factory(),
            'status_id' => ServiceRequestStatus::inRandomOrder()->first() ?? ServiceRequestStatus::factory(),
            'type_id' => ServiceRequestType::inRandomOrder()->first() ?? ServiceRequestType::factory(),
            'priority_id' => ServiceRequestPriority::inRandomOrder()->first() ?? ServiceRequestPriority::factory(),
            'assigned_to_id' => User::factory(),
            'created_by_id' => User::factory(),
        ];
    }
}
