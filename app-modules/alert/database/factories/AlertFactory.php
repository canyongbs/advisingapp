<?php

namespace Assist\Alert\Database\Factories;

use Assist\Alert\Models\Alert;
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
        $concern = $this->faker->randomElement([new Student(), new Prospect()]);

        return [
            'concern_id' => $concern::factory(),
            'concern_type' => $concern->getMorphClass(),
            'description' => $this->faker->sentence,
            'severity' => $this->faker->randomElement(AlertSeverity::cases())->value,
            'suggested_intervention' => $this->faker->sentence,
        ];
    }
}
