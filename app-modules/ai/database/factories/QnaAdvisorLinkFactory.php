<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QnaAdvisorLink>
 */
class QnaAdvisorLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
            'advisor_id' => QnaAdvisor::factory(),
            'parsing_results' => null,
            'is_current' => true,
        ];
    }
}
