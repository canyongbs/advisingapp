<?php

namespace Database\Factories;

use App\Models\Pronouns;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pronouns>
 */
class PronounsFactory extends Factory
{
    protected $model = Pronouns::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->word(),
        ];
    }
}
