<?php

namespace Assist\Form\Database\Factories;

use Assist\Form\Models\Form;
use Assist\Form\Models\FormField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Form>
 */
class FormFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->sentences(asText: true),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Form $form) {
            if ($form->fields()->doesntExist()) {
                $form->fields()->createMany(FormField::factory()->count(3)->make()->toArray());
            }

            if ($form->submissions()->doesntExist()) {
                for ($i = 0; $i < rand(1, 3); $i++) {
                    $content = $form->fields->mapWithKeys(function ($field) {
                        $content = match ($field->type) {
                            'select' => collect($field->config['options'])->keys()->random(),
                            default => fake()->words(rand(1, 10), true),
                        };

                        return [$field->key => $content];
                    });

                    $form->submissions()->create(['content' => $content]);
                }
            }
        });
    }
}
