<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Ai\Filament\Resources\AiAssistants\Forms;

use AdvisingApp\Ai\Enums\AiAssistantApplication;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Settings\AiCustomAdvisorSettings;
use App\Features\ResourceHubKnowledgeFeature;
use App\Filament\Forms\Components\AvatarUploadOrAiGenerator;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class AiAssistantForm
{
    public function form(Schema | Component $form): Schema | Component
    {
        /** @var User $user */
        $user = auth()->user();

        return $form
            ->schema([
                AvatarUploadOrAiGenerator::make(),
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('application')
                    ->options([
                        AiAssistantApplication::PersonalAssistant->value => 'Custom Advisor',
                    ])
                    ->dehydratedWhenHidden()
                    ->default(AiAssistantApplication::getDefault())
                    ->live()
                    ->afterStateUpdated(fn (Set $set, $state) => filled(AiAssistantApplication::parse($state)) ? $set('model', AiAssistantApplication::parse($state)->getDefaultModel()->value) : null)
                    ->required()
                    ->enum(AiAssistantApplication::class)
                    ->columnStart(1)
                    ->visible(auth()->user()->isSuperAdmin())
                    ->disabledOn('edit'),
                Select::make('model')
                    ->reactive()
                    ->options(fn (AiModel|string|null $state) => array_unique([
                        ...AiModelApplicabilityFeature::CustomAdvisors->getModelsAsSelectOptions(),
                        ...match (true) {
                            $state instanceof AiModel => [$state->value => $state->getLabel()],
                            is_string($state) => [$state => AiModel::parse($state)->getLabel()],
                            default => [],
                        },
                    ]))
                    ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::CustomAdvisors->getModels()))
                    ->searchable()
                    ->required()
                    ->visible(fn (Get $get): bool => filled($get('application')) && auth()->user()->isSuperAdmin())
                    ->disabled(fn (): bool => ! app(AiCustomAdvisorSettings::class)->allow_selection_of_model)
                    ->default(function () {
                        $settings = app(AiCustomAdvisorSettings::class);

                        if ($settings->allow_selection_of_model) {
                            return null;
                        }

                        return $settings->preselected_model;
                    })
                    ->dehydratedWhenHidden(),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->required(),
                Select::make('created_by_id')
                    ->label('Created By')
                    ->relationship('createdBy', 'name')
                    ->visible(auth()->user()->isSuperAdmin()),
                Section::make('Institutional Data')
                    ->schema([
                        Toggle::make('has_resource_hub_knowledge')
                            ->label('Resource Hub'),
                    ])
                    ->visible(fn (): bool => ResourceHubKnowledgeFeature::active()),
                Section::make('Configure AI Advisor')
                    ->description('Design the capability of your advisor by including detailed instructions below.')
                    ->schema([
                        Textarea::make('instructions')
                            ->reactive()
                            ->required()
                            ->maxLength(fn (Get $get): int => (AiModel::parse($get('model')) ?? AiModel::OpenAiGpt4o)->getService()->getMaxAssistantInstructionsLength()),
                    ]),
                Section::make('Confidentiality')
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Checkbox::make('is_confidential')
                            ->label('Confidential')
                            ->live()
                            ->columnSpanFull(),
                        Select::make('ai_assistant_confidential_users')
                            ->relationship('confidentialAccessUsers', 'name')
                            ->preload()
                            ->label('Users')
                            ->multiple()
                            ->exists('users', 'id')
                            ->visible(fn (Get $get) => $get('is_confidential')),
                        Select::make('ai_assistant_confidential_teams')
                            ->relationship('confidentialAccessTeams', 'name')
                            ->preload()
                            ->label('Teams')
                            ->multiple()
                            ->exists('teams', 'id')
                            ->visible(fn (Get $get) => $get('is_confidential')),
                    ]),
            ]);
    }
}
