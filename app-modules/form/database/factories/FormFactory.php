<?php

namespace Assist\Form\Database\Factories;

use Illuminate\Support\Str;
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
            'embed_enabled' => fake()->boolean(),
            'allowed_domains' => [fake()->domainName()],
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Form $form) {
            if ($form->fields()->doesntExist()) {
                $form->fields()->createMany(FormField::factory()->count(3)->make()->toArray());

                $form->content = [
                    'type' => 'doc',
                    'content' => $form->fields->map(fn (FormField $field): array => [
                        'type' => 'tiptapBlock',
                        'attrs' => [
                            'id' => $field->id,
                            'type' => $field->type,
                            'data' => [
                                'label' => $field->label,
                                'isRequired' => $field->is_required,
                                ...$field->config,
                            ],
                        ],
                    ])->all(),
                ];
                $form->save();
            }

            if ($form->submissions()->doesntExist()) {
                for ($i = 0; $i < rand(1, 3); $i++) {
                    $submission = $form->submissions()->create();

                    foreach ($form->fields as $field) {
                        $submission->fields()->attach(
                            $field,
                            ['id' => Str::orderedUuid(), 'response' => fake()->words(rand(1, 10), true)],
                        );
                    }
                }
            }
        });
    }
}
