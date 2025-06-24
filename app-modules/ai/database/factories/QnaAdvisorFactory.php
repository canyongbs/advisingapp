<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\QnaAdvisor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QnaAdvisor>
 */
class QnaAdvisorFactory extends Factory
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
            'instructions' => $this->faker->paragraph(),
            'model' => $this->faker->randomElement(
                array_filter(
                    AiModel::cases(),
                    fn (AiModel $case) => $case !== AiModel::JinaDeepSearchV1
                )
            ),
        ];
    }
}
