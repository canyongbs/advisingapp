<?php

namespace Assist\CaseModule\Database\Factories;

use App\Enums\ColumnColorOptions;
use Assist\CaseModule\Models\CaseItemStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseItemStatus>
 */
class CaseItemStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'color' => $this->faker->randomElement(ColumnColorOptions::cases())->value,
        ];
    }

    public function open(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Open',
                'color' => ColumnColorOptions::SUCCESS->value,
            ];
        });
    }

    public function in_progress(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'In Progress',
                'color' => ColumnColorOptions::INFO->value,
            ];
        });
    }

    public function closed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Closed',
                'color' => ColumnColorOptions::WARNING->value,
            ];
        });
    }
}
