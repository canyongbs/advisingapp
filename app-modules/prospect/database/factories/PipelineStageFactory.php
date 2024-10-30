<?php

namespace AdvisingApp\Prospect\Database\Factories;

use AdvisingApp\Prospect\Models\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends PipelineStage>
 */
class PipelineStageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'order' => rand(1, 5),
        ];
    }
}
