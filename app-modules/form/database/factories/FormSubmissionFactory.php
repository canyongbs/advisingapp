<?php

namespace Assist\Form\Database\Factories;

use Assist\Form\Models\Form;
use Assist\Prospect\Models\Prospect;
use Assist\Form\Models\FormSubmission;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @extends Factory<FormSubmission>
 */
class FormSubmissionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'author_type' => fake()->randomElement([(new Student())->getMorphClass(), (new Prospect())->getMorphClass()]),
            'author_id' => function (array $attributes) {
                $authorClass = Relation::getMorphedModel($attributes['author_type']);

                /** @var Student|Prospect $authorModel */
                $authorModel = new $authorClass();

                $author = $authorClass === Student::class
                    ? Student::inRandomOrder()->first() ?? Student::factory()->create()
                    : $authorModel::factory()->create();

                return $author->getKey();
            },
        ];
    }
}
