<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;

class TextInputFormFieldBlock extends FormFieldBlock
{
    public ?string $icon = 'heroicon-m-minus';

    public static function type(): string
    {
        return 'text_input';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'text',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['string', 'max:255'];
    }
}
