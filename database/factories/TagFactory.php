<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Enums\TagType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'type' => fake()->randomElement(TagType::cases())->value,
        ];
    }
}
