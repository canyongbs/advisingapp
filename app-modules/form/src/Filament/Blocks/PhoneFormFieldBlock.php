<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;

class PhoneFormFieldBlock extends FormFieldBlock
{
    public ?string $label = 'Phone number';

    public ?string $icon = 'heroicon-m-phone';

    public static function type(): string
    {
        return 'phone';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'tel',
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
