<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorMessage;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QnaAdvisorMessage>
 */
class QnaAdvisorMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence(12),
            'thread_id' => QnaAdvisorThread::factory(),
            'author_type' => $this->faker->randomElement([
                Student::class,
                Prospect::class,
                QnaAdvisor::class,
            ]),
            'author_id' => fn (array $attributes) => $attributes['author_type']::factory(),
            'is_advisor' => $this->faker->boolean(),
        ];
    }
}
