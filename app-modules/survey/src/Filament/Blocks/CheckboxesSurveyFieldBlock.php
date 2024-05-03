<?php

namespace AdvisingApp\Survey\Filament\Blocks;

use AdvisingApp\Form\Filament\Blocks\CheckboxesFormFieldBlock;

class CheckboxesSurveyFieldBlock extends CheckboxesFormFieldBlock
{
    public function getLabel(): string
    {
        return 'Multiple Choice';
    }
}
