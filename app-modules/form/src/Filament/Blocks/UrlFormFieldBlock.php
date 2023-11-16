<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;

class UrlFormFieldBlock extends FormFieldBlock
{
    public string $rendered = 'form::blocks.submissions.url';

    public ?string $label = 'URL';

    public ?string $icon = 'heroicon-m-link';

    public static function type(): string
    {
        return 'url';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'url',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['url', 'max:255'];
    }
}
