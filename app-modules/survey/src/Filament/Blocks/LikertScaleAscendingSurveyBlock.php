<?php

namespace AdvisingApp\Survey\Filament\Blocks;

class LikertScaleAscendingSurveyBlock extends LikertScaleDescendingSurveyBlock
{
    public ?string $label = 'Likert Scale (Ascending)';

    public static function type(): string
    {
        return 'likert_ascending';
    }

    protected function values(): array
    {
        return collect(parent::values())->reverse()->toArray();
    }
}
