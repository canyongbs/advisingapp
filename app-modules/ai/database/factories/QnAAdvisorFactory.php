<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\QnAAdvisor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QnAAdvisor>
 */
class QnAAdvisorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'model' => $this->faker->randomElement(AiModel::cases()),
        ];
    }
}
