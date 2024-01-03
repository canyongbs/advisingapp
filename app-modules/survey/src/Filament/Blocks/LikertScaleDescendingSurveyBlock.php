<?php

namespace AdvisingApp\Survey\Filament\Blocks;

use Filament\Forms\Components\KeyValue;
use AdvisingApp\Form\Filament\Blocks\RadioFormFieldBlock;

class LikertScaleDescendingSurveyBlock extends RadioFormFieldBlock
{
    public ?string $label = 'Likert Scale (Descending)';

    public string $width = '2xl';

    public static function type(): string
    {
        return 'likert_descending';
    }

    public function fields(): array
    {
        return [
            KeyValue::make('options')
                ->keyLabel('Value')
                ->valueLabel('Label')
                ->default($this->values())
                ->addable(false)
                ->deletable(false)
                ->editableKeys(false),
        ];
    }

    protected function values(): array
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
