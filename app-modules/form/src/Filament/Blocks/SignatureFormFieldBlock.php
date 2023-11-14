<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;

class SignatureFormFieldBlock extends FormFieldBlock
{
    public string $preview = 'form::blocks.previews.signature';

    public string $rendered = 'form::blocks.submissions.signature';

    public static function type(): string
    {
        return 'signature';
    }

    public function fields(): array
    {
        return [];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'signature',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }
}
