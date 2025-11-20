<?php

namespace AdvisingApp\ResourceHub\Database\Factories;

use AdvisingApp\ResourceHub\Enums\ConcernStatus;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\ResourceHub\Models\ResourceHubArticleConcern;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResourceHubArticleConcern>
 */
class ResourceHubArticleConcernFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    { 
        return [
            'description' => $this->faker->words(3, true),
            'created_by_id' => User::factory(),
            'status' => $this->faker->randomElement(ConcernStatus::cases()),
            'resource_hub_article_id' => ResourceHubArticle::factory(),
        ];
    }
}
