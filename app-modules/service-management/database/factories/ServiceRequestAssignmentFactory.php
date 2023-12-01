<?php

namespace Assist\ServiceManagement\Database\Factories;

use App\Models\User;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\ServiceManagement\Enums\ServiceRequestAssignmentStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\ServiceManagement\Models\ServiceRequestAssignment>
 */
class ServiceRequestAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_request_id' => ServiceRequest::factory(),
            'user_id' => User::factory(),
            'assigned_at' => fake()->dateTimeBetween('-1 year', now()),
        ];
    }

    public function active(): self
    {
        return $this->state([
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);
    }
}
