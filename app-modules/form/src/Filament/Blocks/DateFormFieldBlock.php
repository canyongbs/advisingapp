<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;

class DateFormFieldBlock extends FormFieldBlock
{
    public string $rendered = 'form::blocks.submissions.date';

    public static function type(): string
    {
        return 'date';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'date',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['date'];
    }
}
