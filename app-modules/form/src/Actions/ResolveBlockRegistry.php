<?php

namespace AdvisingApp\Form\Actions;

use AdvisingApp\Form\Models\Form;
use AdvisingApp\Survey\Models\Survey;
use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\Survey\Filament\Blocks\SurveyFieldBlockRegistry;

class ResolveBlockRegistry
{
    public function __invoke(Submissible $submissible): array
    {
        return match ($submissible::class) {
            Form::class, Application::class => FormFieldBlockRegistry::keyByType(),
            Survey::class => SurveyFieldBlockRegistry::keyByType(),
        };
    }
}
