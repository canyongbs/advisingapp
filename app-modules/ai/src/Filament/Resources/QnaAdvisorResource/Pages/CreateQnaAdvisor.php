<?php

namespace AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Settings\AiQnaAdvisorSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\Rule;

class CreateQnaAdvisor extends CreateRecord
{
    protected static string $resource = QnaAdvisorResource::class;

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
                    ->live()
                    ->options(AiModelApplicabilityFeature::QuestionAndAnswerAdvisor->getModelsAsSelectOptions())
                    ->searchable()
                    ->required()
                    ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::QuestionAndAnswerAdvisor->getModels()))
                    ->disabled(fn (): bool => ! app(AiQnaAdvisorSettings::class)->allow_selection_of_model)
                    ->visible(auth()->user()->isSuperAdmin())
                    ->default(function () {
                        $settings = app(AiQnaAdvisorSettings::class);

                        if ($settings->allow_selection_of_model) {
                            return null;
                        }

                        return $settings->preselected_model;
                    }),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->required(),
                Section::make('Configure AI Advisor')
                    ->description('Design the capability of your advisor by including detailed instructions below.')
                    ->visible(auth()->user()->isSuperAdmin())
                    ->schema([
                        Textarea::make('instructions')
                            ->required()
                            ->disabled(fn (): bool => ! app(AiQnaAdvisorSettings::class)->allow_selection_of_model)
                            ->maxLength(fn (Get $get): int => (AiModel::parse($get('model')) ?? AiModel::OpenAiGpt4o)->getService()->getMaxAssistantInstructionsLength())
                            ->default(function () {
                                $settings = app(AiQnaAdvisorSettings::class);

                                if ($settings->allow_selection_of_model) {
                                    return null;
                                }

                                return $settings->instructions;
                            }),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $settings = app(AiQnaAdvisorSettings::class);

        if (! $settings->allow_selection_of_model) {
            $data['model'] = $settings->preselected_model ?? $data['model'];
            $data['instructions'] = $settings->instructions ?? $data['instructions'];
        }

        return $data;
    }
}
