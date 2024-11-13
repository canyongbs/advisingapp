<?php

namespace AdvisingApp\Alert\Database\Factories;

use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\Alert\Models\Model>
 */
class AlertStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'classification' => SystemAlertStatusClassification::Active,
            'name' => fake()->word(),
        ];
    }

    public function resolved(): self
    {
        return $this->state(fn() => ['classification' => SystemAlertStatusClassification::Resolved]);
    }

    public function canceled(): self
    {
        return $this->state(fn() => ['classification' => SystemAlertStatusClassification::Canceled]);
    }
}
