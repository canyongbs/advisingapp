<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Settings\AiQnAAdvisorSettings;
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
                    ->options(AiModelApplicabilityFeature::QuestionAndAnswerAdvisor->getModelsAsSelectOptions())
                    ->searchable()
                    ->required()
                    ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::QuestionAndAnswerAdvisor->getModels()))
                    ->disabled(fn (): bool => ! app(AiQnAAdvisorSettings::class)->allow_selection_of_model)
                    ->default(function () {
                        $settings = app(AiQnAAdvisorSettings::class);

                        if ($settings->allow_selection_of_model) {
                            return null;
                        }

                        return $settings->preselected_model;
                    }),
                Textarea::make('description')
                    ->required(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $settings = app(AiQnAAdvisorSettings::class);

        if (! $settings->allow_selection_of_model) {
            $data['model'] = $settings->preselected_model ?? $data['model'];
        }

        return $data;
    }
}
