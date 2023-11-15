<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Forms\Components\KeyValue;

class RadioFormFieldBlock extends FormFieldBlock
{
    public string $preview = 'form::blocks.previews.radio';

    public string $rendered = 'form::blocks.submissions.radio';

    public ?string $icon = 'heroicon-m-list-bullet';

    public static function type(): string
    {
        return 'radio';
    }

    public function fields(): array
    {
        return [
            KeyValue::make('options')
                ->keyLabel('Value')
                ->valueLabel('Label'),
        ];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'radio',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
            'options' => $field->config['options'],
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return [
            'string',
            'in:' . collect($field->config['options'])->keys()->join(','),
        ];
    }
}
