<?php

namespace Assist\Form\Actions;

use Illuminate\Support\Arr;
use Assist\Form\Models\Form;
use Assist\Form\Models\FormField;
use Illuminate\Database\Eloquent\Collection;
use Assist\Form\Filament\Blocks\FormFieldBlockRegistry;

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
        $blocks = FormFieldBlockRegistry::keyByType();

        return $fields
            ->mapWithKeys(function (FormField $field) use ($blocks) {
                $rules = collect();

                if ($field->required) {
                    $rules->push('required');
                }

                return [$field->key => $rules
                    ->merge($blocks[$field->type]::getValidationRules($field))
                    ->all()];
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
