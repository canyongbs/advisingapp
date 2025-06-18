<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Models\QnAAdvisorCategory;
use AdvisingApp\Ai\Models\QnaAdvisorQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QnaAdvisorQuestion>
 */
class QnaAdvisorQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence(),
            'answer' => $this->faker->paragraph(),
            'category_id' => QnAAdvisorCategory::factory(),
        ];
    }
}
