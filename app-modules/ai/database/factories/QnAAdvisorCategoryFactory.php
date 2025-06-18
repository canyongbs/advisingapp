<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Ai\Models\QnAAdvisorCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QnAAdvisorCategory>
 */
class QnAAdvisorCategoryFactory extends Factory
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
            'qn_a_advisor_id' => QnAAdvisor::factory(),
        ];
    }
}
