<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QnaAdvisorCategory>
 */
class QnaAdvisorCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'qna_advisor_id' => QnaAdvisor::factory(),
        ];
    }
}
