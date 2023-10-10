<?php

namespace Assist\Alert\Database\Factories;

use Assist\Alert\Models\Alert;
use Assist\Alert\Enums\AlertStatus;
use Assist\Prospect\Models\Prospect;
use Assist\Alert\Enums\AlertSeverity;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Factory<Alert>
 */
class AlertFactory extends Factory
{
    public function definition(): array
    {
        return [
            'concern_type' => fake()->randomElement([(new Student())->getMorphClass(), (new Prospect())->getMorphClass()]),
            'concern_id' => function (array $attributes) {
                $concernClass = Relation::getMorphedModel($attributes['concern_type']);

                /** @var Student|Prospect $concernModel */
                $concernModel = new $concernClass();

                $concern = $concernClass === Student::class
                    ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                    : $concernModel::factory()->create();

                return $concern->getKey();
            },
            'description' => fake()->sentence(),
            'severity' => fake()->randomElement(AlertSeverity::cases()),
            'status' => fake()->randomElement(AlertStatus::cases()),
            'suggested_intervention' => fake()->sentence(),
        ];
    }
}
