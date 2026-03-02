<?php

namespace AdvisingApp\Alert\Database\Factories\Configurations;

use AdvisingApp\Alert\Configurations\LowEarnedCreditPercentageAlertConfiguration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LowEarnedCreditPercentageAlertConfiguration>
 */
class LowEarnedCreditPercentageAlertConfigurationFactory extends Factory
{
    protected $model = LowEarnedCreditPercentageAlertConfiguration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'minimum_earned_credit_percentage' => $this->faker->numberBetween(1, 100),
        ];
    }
}
