<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;

class EmailFormFieldBlock extends FormFieldBlock
{
    public ?string $label = 'Email address';

    public static function type(): string
    {
        return 'email';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'email',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['string', 'email', 'max:255'];
    }
}
