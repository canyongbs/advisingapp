<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;

class CheckboxFormFieldBlock extends FormFieldBlock
{
    public string $preview = 'form::blocks.previews.checkbox';

    public string $rendered = 'form::blocks.submissions.checkbox';

    public static function type(): string
    {
        return 'checkbox';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'checkbox',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['boolean'];
    }
}
