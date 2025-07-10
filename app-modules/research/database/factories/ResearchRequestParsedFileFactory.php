<?php

namespace AdvisingApp\Research\Database\Factories;

use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestParsedFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResearchRequestParsedFile>
 */
class ResearchRequestParsedFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'research_request_id' => ResearchRequest::factory(),
            'uploaded_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'results' => $this->faker->text(),
        ];
    }
}
