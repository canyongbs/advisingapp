<?php

namespace Assist\ServiceManagement\Database\Factories;

use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;

/**
 * @extends Factory<ServiceRequestUpdate>
 */
class ServiceRequestUpdateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_request_id' => ServiceRequest::factory(),
            'update' => $this->faker->sentence(),
            'internal' => $this->faker->boolean(),
            'direction' => $this->faker->randomElement(ServiceRequestUpdateDirection::cases())->value,
        ];
    }
}
