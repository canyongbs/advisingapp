<?php

namespace AdvisingApp\Project\Database\Factories;

use AdvisingApp\Project\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
  /**
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => str($this->faker->unique()->words(3, true))->title()->toString(),
      'description' => $this->faker->sentence(),
    ];
  }
}
