<?php

namespace Assist\Case\Database\Factories;

use Assist\Case\Models\ServiceRequest;
use Assist\Case\Models\ServiceRequestUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\Case\Enums\ServiceRequestUpdateDirection;

/**
 * @extends Factory<ServiceRequestUpdate>
 */
class CaseUpdateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'case_id' => ServiceRequest::factory(),
            'update' => $this->faker->sentence(),
            'internal' => $this->faker->boolean(),
            'direction' => $this->faker->randomElement(ServiceRequestUpdateDirection::cases())->value,
        ];
    }
}
