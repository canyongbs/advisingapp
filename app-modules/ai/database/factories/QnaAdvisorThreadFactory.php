<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QnaAdvisorThread>
 */
class QnaAdvisorThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'advisor_id' => QnaAdvisor::factory(),
        ];
    }
}
