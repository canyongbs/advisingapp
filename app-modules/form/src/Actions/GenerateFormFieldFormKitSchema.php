<?php

namespace Assist\Form\Actions;

use Assist\Form\Models\FormField;

class GenerateFormFieldFormKitSchema
{
    public function handle(FormField $formField): object
    {
        return match ($formField->type) {
            'text_input' => (object) [
                '$formkit' => 'text',
                'label' => $formField->label,
                'name' => $formField->key,
                'required' => $formField->required,
            ],
            'text_area' => (object) [
                '$formkit' => 'textarea',
                'label' => $formField->label,
                'name' => $formField->key,
                'required' => $formField->required,
            ],
            'select' => (object) [
                '$formkit' => 'select',
                'label' => $formField['label'],
                'name' => $formField->key,
                'required' => $formField->required,
                'options' => $formField->config['options'],
            ],
        };
    }
}
