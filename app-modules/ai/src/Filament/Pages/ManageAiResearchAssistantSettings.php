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

namespace AdvisingApp\Ai\Filament\Pages;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Enums\AiResearchReasoningEffort;
use AdvisingApp\Ai\Settings\AiResearchAssistantSettings;
use App\Filament\Clusters\GlobalArtificialIntelligence;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class ManageAiResearchAssistantSettings extends SettingsPage
{
    protected static string $settings = AiResearchAssistantSettings::class;

    protected static ?string $title = 'Research Advisor Settings';

    protected static ?string $navigationLabel = 'Research Advisor';

    protected static ?int $navigationSort = 30;

    protected static ?string $cluster = GlobalArtificialIntelligence::class;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        assert($user instanceof User);

        return $user->canAccessAiSettings();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('discovery_model')
                    ->options(fn (AiModel|string|null $state) => array_unique([
                        ...AiModelApplicabilityFeature::IntegratedAdvisor->getModelsAsSelectOptions(),
                        ...match (true) {
                            $state instanceof AiModel => [$state->value => $state->getLabel()],
                            is_string($state) => [$state => AiModel::parse($state)->getLabel()],
                            default => [],
                        },
                    ]))
                    ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::IntegratedAdvisor->getModels()))
                    ->searchable()
                    ->helperText('Used for the generation of the pre-research questions.')
                    ->required(),
                Select::make('research_model')
                    ->options(fn (AiModel|string|null $state) => array_unique([
                        ...AiModelApplicabilityFeature::ResearchAdvisor->getModelsAsSelectOptions(),
                        ...match (true) {
                            $state instanceof AiModel => [$state->value => $state->getLabel()],
                            is_string($state) => [$state => AiModel::parse($state)->getLabel()],
                            default => [],
                        },
                    ]))
                    ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::ResearchAdvisor->getModels()))
                    ->searchable()
                    ->helperText('Used for the generation of the research report.')
                    ->required(),
                Select::make('reasoning_effort')
                    ->options(AiResearchReasoningEffort::class)
                    ->searchable()
                    ->helperText('Constrains effort on reasoning for reasoning models. Currently supported values are low, medium, and high. Reducing reasoning effort can result in faster responses and fewer tokens used on reasoning in a response.'),
                Textarea::make('context')
                    ->rows(10)
                    ->label('Institutional Context'),
            ])
            ->columns(1);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (filled($data['discovery_model'] ?? null)) {
            $data['discovery_model'] = AiModel::parse($data['discovery_model']);
        }

        if (filled($data['research_model'] ?? null)) {
            $data['research_model'] = AiModel::parse($data['research_model']);
        }

        return parent::mutateFormDataBeforeSave($data);
    }
}
