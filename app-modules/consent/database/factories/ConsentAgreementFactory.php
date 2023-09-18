<?php

namespace Assist\Consent\Database\Factories;

use Assist\Consent\Enums\ConsentAgreementType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Assist\Consent\Models\ConsentAgreement>
 */
class ConsentAgreementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(ConsentAgreementType::cases()),
            'title' => fake()->catchPhrase(),
            'description' => fake()->paragraph(),
            'body' => fake()->paragraphs(3, true),
        ];
    }
}
