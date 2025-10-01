<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Models\QnaAdvisorMessage;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        $morphTypes = [
            (new Student())->getMorphClass(),
            (new Prospect())->getMorphClass(),
        ];

        return [
            'content' => $this->faker->sentence(12),
            'thread_id' => QnaAdvisorThread::factory(),
            'author_type' => $this->faker->randomElement($morphTypes),
            'author_id' => fn (array $attributes) => (new (Relation::getMorphedModel($attributes['author_type'])))->factory(),
            'is_advisor' => $this->faker->boolean(),
        ];
    }
}
