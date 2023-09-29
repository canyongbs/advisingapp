<?php

namespace Assist\Alert\Database\Factories;

use Assist\Alert\Models\Alert;
use Assist\Alert\Enums\AlertStatus;
use Assist\Prospect\Models\Prospect;
use Assist\Alert\Enums\AlertSeverity;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alert>
 */
class AlertFactory extends Factory
{
    public function definition(): array
    {
        $concern = fake()->randomElement([new Student(), new Prospect()]);

        return [
            'concern_id' => $concern::factory(),
            'concern_type' => $concern->getMorphClass(),
            'description' => fake()->sentence(),
            'severity' => fake()->randomElement(AlertSeverity::cases()),
            'status' => fake()->randomElement(AlertStatus::cases()),
            'suggested_intervention' => fake()->sentence(),
        ];
    }
}
