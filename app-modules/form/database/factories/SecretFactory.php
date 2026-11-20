<?php

namespace AdvisingApp\Form\Database\Factories;

use AdvisingApp\Form\Models\Secret;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

/**
 * @extends Factory<Secret>
 */
class SecretFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => Crypt::encryptString($this->faker->password()),
        ];
    }
}
