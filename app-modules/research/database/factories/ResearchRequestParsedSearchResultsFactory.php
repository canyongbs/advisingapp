<?php

namespace AdvisingApp\Research\Database\Factories;

use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestParsedSearchResults;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResearchRequestParsedSearchResults>
 */
class ResearchRequestParsedSearchResultsFactory extends Factory
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
            'search_query' => $this->faker->sentence(),
        ];
    }
}
