<?php

namespace AdvisingApp\BasicNeeds\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\BasicNeeds\Models\BasicNeedCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\Prospect\Models\Program>
 */
class BasicNeedProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function getAvailabilityValue()
    {
        return collect([
            $this->faker->numberBetween(1, 24) . ' Hours',
            $this->faker->numberBetween(1, 100) . ' Days',
        ])->random();
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'basic_need_category_id' => BasicNeedCategory::factory(),
            'contact_person' => $this->faker->name(),
            'contact_email' => $this->faker->email(),
            'contact_phone' => $this->faker->numerify('+1 ### ### ####'),
            'location' => $this->faker->city(),
            'availability' => $this->getAvailabilityValue(),
            'eligibility_criteria' => $this->faker->sentence(),
            'application_process' => $this->faker->paragraph(),
        ];
    }
}
