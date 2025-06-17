<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Settings\AiCustomAdvisorSettings;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\Rule;

class CreateQnAAdvisor extends CreateRecord
{
    protected static string $resource = QnAAdvisorResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                 SpatieMediaLibraryFileUpload::make('avatar')
                    ->label('Avatar')
                    ->disk('s3')
                    ->collection('avatar')
                    ->visibility('private')
                    ->avatar()
                    ->columnSpanFull()
                    ->acceptedFileTypes([
                        'image/png',
                        'image/jpeg',
                        'image/gif',
                    ]),
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->maxLength(255),
                Select::make('model')
                    ->reactive()
                    ->options(AiModelApplicabilityFeature::CustomAdvisors->getModelsAsSelectOptions())
                    ->searchable()
                    ->required()
                    ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::CustomAdvisors->getModels())),
                Textarea::make('description')
                    ->required(),
            ]);
    }
}
