<?php

namespace AdvisingApp\BasicNeeds\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\BasicNeeds\Models\BasicNeedsCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\BasicNeeds\Models\BasicNeedsProgram>
 */
class BasicNeedsProgramFactory extends Factory
{
    /**
    * Define the model's default state.
    *
    * @return array<string, mixed>
    */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->paragraph(),
            'basic_need_category_id' => BasicNeedsCategory::factory(),
            'contact_person' => fake()->name(),
            'contact_email' => fake()->email(),
            'contact_phone' => fake()->numerify('+1 ### ### ####'),
            'location' => fake()->city(),
            'availability' => fake()->randomElement(),
            'eligibility_criteria' => fake()->sentence(),
            'application_process' => fake()->paragraph(),
        ];
    }
}
