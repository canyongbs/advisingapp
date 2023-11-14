<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;

class TimeFormFieldBlock extends FormFieldBlock
{
    public string $rendered = 'form::blocks.submissions.time';

    public static function type(): string
    {
        return 'time';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'time',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }
}
