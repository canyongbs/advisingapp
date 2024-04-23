<?php

namespace AdvisingApp\Assistant\Filament\Resources\AiAssistantResource\Pages;

use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Assistant\Filament\Resources\AiAssistantResource;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Forms\AiAssistantForm;

class CreateAiAssistant extends CreateRecord
{
    protected static string $resource = AiAssistantResource::class;

    public function form(Form $form): Form
    {
        return resolve(AiAssistantForm::class)->form($form);
    }
}
