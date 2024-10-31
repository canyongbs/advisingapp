<?php

namespace AdvisingApp\Prospect\Database\Factories;

use AdvisingApp\Prospect\Models\Pipeline;
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
            'pipeline_id' => Pipeline::factory(),
            'is_default' => fake()->boolean(),
            'order' => mt_rand(0, 5),
        ];
    }
}
