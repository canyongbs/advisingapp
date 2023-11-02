<?php

namespace Assist\Form\Actions;

use Illuminate\Support\Arr;
use Assist\Form\Models\Form;
use Assist\Form\Models\FormField;
use Illuminate\Database\Eloquent\Collection;

class GenerateFormValidation
{
    public function __invoke(Form $form): array
    {
        if ($form->is_wizard) {
            return $this->wizardRules($form);
        }

        return $this->fields($form->fields);
    }

    public function fields(Collection $fields): array
    {
        return $fields->mapWithKeys(fn (FormField $field) => [$field->key => $this->field($field)])->all();
    }

    public function field(FormField $field): array
    {
        $rules = collect();

        if ($field->required) {
            $rules->push('required');
        }

        return $rules
            ->merge(match ($field['type']) {
                'text_input' => [
                    'string',
                    'max:255',
                ],
                'text_area' => [
                    'string',
                    'max:65535',
                ],
                'select' => [
                    'string',
                    'in:' . collect($field['config']['options'])->keys()->join(','),
                ],
                default => null,
            })
            ->all();
    }

    public function wizardRules(Form $form): array
    {
        $rules = collect();

        foreach ($form->steps as $step) {
            $rules->merge(
                Arr::prependKeysWith(
                    $this->fields($step->fields),
                    prependWith: "{$step->label}.",
                ),
            );
        }

        return $rules->all();
    }
}
