<?php

namespace AdvisingApp\Research\Database\Factories;

use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestParsedLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResearchRequestParsedLink>
 */
class ResearchRequestParsedLinkFactory extends Factory
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
            'results' => $this->faker->text(),
            'url' => $this->faker->url(),
        ];
    }
}
