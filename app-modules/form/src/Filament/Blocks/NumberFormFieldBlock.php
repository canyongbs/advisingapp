<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;

class NumberFormFieldBlock extends FormFieldBlock
{
    public string $rendered = 'form::blocks.submissions.number';

    public ?string $icon = 'heroicon-m-variable';

    public static function type(): string
    {
        return 'number';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'number',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['numeric'];
    }
}
