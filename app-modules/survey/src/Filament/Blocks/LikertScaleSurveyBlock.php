<?php

namespace AdvisingApp\Survey\Filament\Blocks;

use AdvisingApp\Form\Models\SubmissibleField;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlock;

class LikertScaleSurveyBlock extends FormFieldBlock
{
    public ?string $label = 'Likert Scale';

    public string $preview = 'survey::blocks.previews.likert';

    public string $rendered = 'survey::blocks.submissions.likert';

    public ?string $icon = 'heroicon-m-list-bullet';

    public static function type(): string
    {
        return 'likert';
    }

    public static function getFormKitSchema(SubmissibleField $field): array
    {
        return [
            '$formkit' => 'radio',
            'label' => $field->label,
            'name' => $field->getKey(),
            ...($field->is_required ? ['validation' => 'required'] : []),
            'options' => static::options(),
        ];
    }

    public static function getValidationRules(SubmissibleField $field): array
    {
        return [
            'string',
            'in:' . collect(static::options())->keys()->join(','),
        ];
    }

    public static function options(): array
    {
        return [
            'strongly-agree' => 'Strongly agree',
            'agree' => 'Agree',
            'neutral' => 'Neither agree nor disagree',
            'disagree' => 'Disagree',
            'strongly-disagree' => 'Strongly disagree',
        ];
    }
}
