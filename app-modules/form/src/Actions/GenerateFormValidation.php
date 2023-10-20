<?php

namespace Assist\Form\Actions;

use Assist\Form\Models\Form;

class GenerateFormValidation
{
    public function handle(Form $form): array
    {
        return $form->fields->mapWithKeys(function ($field) {
            $rules = collect();

            if ($field['required']) {
                $rules->push('required');
            }

            return [$field['key'] => $rules->merge(
                match ($field['type']) {
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
                }
            )->toArray()];
        })->toArray();
    }
}
