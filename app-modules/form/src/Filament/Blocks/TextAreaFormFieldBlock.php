<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;

class TextAreaFormFieldBlock extends FormFieldBlock
{
    public string $preview = 'form::blocks.previews.textarea';

    public static function type(): string
    {
        return 'text_area';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'textarea',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['string', 'max:65535'];
    }
}
