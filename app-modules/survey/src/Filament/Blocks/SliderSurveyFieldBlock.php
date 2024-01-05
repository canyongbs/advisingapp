<?php

namespace AdvisingApp\Survey\Filament\Blocks;

use AdvisingApp\Form\Models\SubmissibleField;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlock;

class SliderSurveyFieldBlock extends FormFieldBlock
{
    public string $preview = 'survey::blocks.previews.slider';

    public static function type(): string
    {
        return 'slider';
    }

    public static function getFormKitSchema(SubmissibleField $field): array
    {
        return [
            '$formkit' => 'slider',
            'label' => $field->label,
            'name' => $field->getKey(),
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(SubmissibleField $field): array
    {
        return [
            'integer',
        ];
    }
}
