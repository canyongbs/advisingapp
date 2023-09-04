<?php

namespace Assist\Case\Database\Factories;

use Assist\Case\Models\ServiceRequest;
use Assist\Case\Enums\CaseUpdateDirection;
use Assist\Case\Models\ServiceRequestUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'direction' => $this->faker->randomElement(CaseUpdateDirection::cases())->value,
        ];
    }
}
