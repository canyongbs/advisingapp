<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Ai\Actions\ResetAiServiceIdsForModel;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Jobs\ReInitializeAiModel;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use App\Filament\Clusters\GlobalArtificialIntelligence;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ManageAiIntegrationsSettings extends SettingsPage
{
    protected static string $settings = AiIntegrationsSettings::class;

    protected static ?string $title = 'Cognitive Services Settings';

    protected static ?string $navigationLabel = 'Cognitive Services';

    protected static ?int $navigationSort = 50;

    protected static ?string $cluster = GlobalArtificialIntelligence::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isSuperAdmin();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Section::make('Azure OpenAI')
                    ->collapsible()
                    ->schema([
                        Section::make('GPT 4o')
                            ->collapsible()
                            ->schema([
                                TextInput::make('open_ai_gpt_4o_model_name')
                                    ->label('Model Name')
                                    ->placeholder('Canyon 4o')
                                    ->string()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('open_ai_gpt_4o_base_uri')
                                    ->label('Base URI')
                                    ->placeholder('https://example.openai.azure.com/openai')
                                    ->url(),
                                TextInput::make('open_ai_gpt_4o_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                TextInput::make('open_ai_gpt_4o_model')
                                    ->label('Model'),
                                TextInput::make('open_ai_gpt_4o_image_generation_model_name')
                                    ->label('Image Generation Model'),
                                Select::make('open_ai_gpt_4o_applicable_features')
                                    ->label('Applicability')
                                    ->options(AiModelApplicabilityFeature::class)
                                    ->multiple()
                                    ->nestedRecursiveRules([Rule::enum(AiModelApplicabilityFeature::class)]),
                                Checkbox::make('is_open_ai_gpt_4o_responses_api_enabled')
                                    ->label('Enable Responses API')
                                    ->helperText('Use the OpenAI Responses API for generating text. The Base URI must use /openai/v1 instead of /openai.'),
                            ]),
                        Section::make('GPT 4o mini')
                            ->collapsible()
                            ->schema([
                                TextInput::make('open_ai_gpt_4o_mini_model_name')
                                    ->label('Model Name')
                                    ->placeholder('Canyon 4o mini')
                                    ->string()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('open_ai_gpt_4o_mini_base_uri')
                                    ->label('Base URI')
                                    ->placeholder('https://example.openai.azure.com/openai')
                                    ->url(),
                                TextInput::make('open_ai_gpt_4o_mini_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                TextInput::make('open_ai_gpt_4o_mini_model')
                                    ->label('Model'),
                                TextInput::make('open_ai_gpt_4o_mini_image_generation_model')
                                    ->label('Image Generation Model'),
                                Select::make('open_ai_gpt_4o_mini_applicable_features')
                                    ->label('Applicability')
                                    ->options(AiModelApplicabilityFeature::class)
                                    ->multiple()
                                    ->nestedRecursiveRules([Rule::enum(AiModelApplicabilityFeature::class)]),
                                Checkbox::make('is_open_ai_gpt_4o_mini_responses_api_enabled')
                                    ->label('Enable Responses API')
                                    ->helperText('Use the OpenAI Responses API for generating text. The Base URI must use /openai/v1 instead of /openai.'),
                            ]),
                        Section::make('GPT o1 mini')
                            ->collapsible()
                            ->schema([
                                TextInput::make('open_ai_gpt_o1_mini_model_name')
                                    ->label('Model Name')
                                    ->placeholder('Canyon o1 mini')
                                    ->string()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('open_ai_gpt_o1_mini_base_uri')
                                    ->label('Base URI')
                                    ->placeholder('https://example.openai.azure.com/openai')
                                    ->url(),
                                TextInput::make('open_ai_gpt_o1_mini_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                TextInput::make('open_ai_gpt_o1_mini_model')
                                    ->label('Model'),
                                Select::make('open_ai_gpt_o1_mini_applicable_features')
                                    ->label('Applicability')
                                    ->options(AiModelApplicabilityFeature::class)
                                    ->multiple()
                                    ->nestedRecursiveRules([Rule::enum(AiModelApplicabilityFeature::class)]),
                            ]),
                        Section::make('GPT o3')
                            ->collapsible()
                            ->schema([
                                TextInput::make('open_ai_gpt_o3_model_name')
                                    ->label('Model Name')
                                    ->placeholder('Canyon o3')
                                    ->string()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('open_ai_gpt_o3_base_uri')
                                    ->label('Base URI')
                                    ->placeholder('https://example.openai.azure.com/openai/v1')
                                    ->url(),
                                TextInput::make('open_ai_gpt_o3_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                TextInput::make('open_ai_gpt_o3_model')
                                    ->label('Model'),
                                TextInput::make('open_ai_gpt_o3_image_generation_model')
                                    ->label('Image Generation Model'),
                                Select::make('open_ai_gpt_o3_applicable_features')
                                    ->label('Applicability')
                                    ->options(AiModelApplicabilityFeature::class)
                                    ->multiple()
                                    ->nestedRecursiveRules([Rule::enum(AiModelApplicabilityFeature::class)]),
                            ]),
                        Section::make('GPT o3 mini')
                            ->collapsible()
                            ->schema([
                                TextInput::make('open_ai_gpt_o3_mini_model_name')
                                    ->label('Model Name')
                                    ->placeholder('Canyon o3 mini')
                                    ->string()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('open_ai_gpt_o3_mini_base_uri')
                                    ->label('Base URI')
                                    ->placeholder('https://example.openai.azure.com/openai')
                                    ->url(),
                                TextInput::make('open_ai_gpt_o3_mini_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                TextInput::make('open_ai_gpt_o3_mini_model')
                                    ->label('Model'),
                                Select::make('open_ai_gpt_o3_mini_applicable_features')
                                    ->label('Applicability')
                                    ->options(AiModelApplicabilityFeature::class)
                                    ->multiple()
                                    ->nestedRecursiveRules([Rule::enum(AiModelApplicabilityFeature::class)]),
                            ]),
                        Section::make('GPT 4.1 mini')
                            ->collapsible()
                            ->schema([
                                TextInput::make('open_ai_gpt_41_mini_model_name')
                                    ->label('Model Name')
                                    ->placeholder('Canyon 4.1 mini')
                                    ->string()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('open_ai_gpt_41_mini_base_uri')
                                    ->label('Base URI')
                                    ->placeholder('https://example.openai.azure.com/openai')
                                    ->url(),
                                TextInput::make('open_ai_gpt_41_mini_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                TextInput::make('open_ai_gpt_41_mini_model')
                                    ->label('Model'),
                                TextInput::make('open_ai_gpt_41_mini_image_generation_model')
                                    ->label('Image Generation Model'),
                                Select::make('open_ai_gpt_41_mini_applicable_features')
                                    ->label('Applicability')
                                    ->options(AiModelApplicabilityFeature::class)
                                    ->multiple()
                                    ->nestedRecursiveRules([Rule::enum(AiModelApplicabilityFeature::class)]),
                                Checkbox::make('is_open_ai_gpt_41_mini_responses_api_enabled')
                                    ->label('Enable Responses API')
                                    ->helperText('Use the OpenAI Responses API for generating text. The Base URI must use /openai/v1 instead of /openai.'),
                            ]),
                        Section::make('GPT 4.1 nano')
                            ->collapsible()
                            ->schema([
                                TextInput::make('open_ai_gpt_41_nano_model_name')
                                    ->label('Model Name')
                                    ->placeholder('Canyon 4.1 nano')
                                    ->string()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('open_ai_gpt_41_nano_base_uri')
                                    ->label('Base URI')
                                    ->placeholder('https://example.openai.azure.com/openai')
                                    ->url(),
                                TextInput::make('open_ai_gpt_41_nano_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                TextInput::make('open_ai_gpt_41_nano_model')
                                    ->label('Model'),
                                TextInput::make('open_ai_gpt_41_nano_image_generation_model')
                                    ->label('Image Generation Model'),
                                Select::make('open_ai_gpt_41_nano_applicable_features')
                                    ->label('Applicability')
                                    ->options(AiModelApplicabilityFeature::class)
                                    ->multiple()
                                    ->nestedRecursiveRules([Rule::enum(AiModelApplicabilityFeature::class)]),
                                Checkbox::make('is_open_ai_gpt_41_nano_responses_api_enabled')
                                    ->label('Enable Responses API')
                                    ->helperText('Use the OpenAI Responses API for generating text. The Base URI must use /openai/v1 instead of /openai.'),
                            ]),
                        Section::make('GPT o4 mini')
                            ->collapsible()
                            ->schema([
                                TextInput::make('open_ai_gpt_o4_mini_model_name')
                                    ->label('Model Name')
                                    ->placeholder('Canyon o4 mini')
                                    ->string()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('open_ai_gpt_o4_mini_base_uri')
                                    ->label('Base URI')
                                    ->placeholder('https://example.openai.azure.com/openai')
                                    ->url(),
                                TextInput::make('open_ai_gpt_o4_mini_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                TextInput::make('open_ai_gpt_o4_mini_model')
                                    ->label('Model'),
                                Select::make('open_ai_gpt_o4_mini_applicable_features')
                                    ->label('Applicability')
                                    ->options(AiModelApplicabilityFeature::class)
                                    ->multiple()
                                    ->nestedRecursiveRules([Rule::enum(AiModelApplicabilityFeature::class)]),
                                Checkbox::make('is_open_ai_gpt_o4_mini_responses_api_enabled')
                                    ->label('Enable Responses API')
                                    ->helperText('Use the OpenAI Responses API for generating text. The Base URI must use /openai/v1 instead of /openai.'),
                            ]),
                    ]),
                Section::make('Jina AI')
                    ->collapsible()
                    ->schema([
                        Section::make('Jina DeepSearch V1')
                            ->collapsible()
                            ->schema([
                                TextInput::make('jina_deepsearch_v1_model_name')
                                    ->label('Model Name')
                                    ->placeholder('Canyon Deep Search')
                                    ->string()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('jina_deepsearch_v1_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                Select::make('jina_deepsearch_v1_applicable_features')
                                    ->label('Applicability')
                                    ->options(AiModelApplicabilityFeature::class)
                                    ->multiple()
                                    ->nestedRecursiveRules([Rule::enum(AiModelApplicabilityFeature::class)]),
                            ]),
                    ]),
                Section::make('LlamaCloud')
                    ->collapsible()
                    ->schema([
                        Section::make('LlamaCloud Parsing Service')
                            ->collapsible()
                            ->schema([
                                TextInput::make('llamaparse_model_name')
                                    ->label('Model Name')
                                    ->placeholder('Canyon Parsing Service')
                                    ->string()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('llamaparse_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                            ]),
                    ]),
            ]);
    }

    public function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->modalHeading('Sync all chats to this new service?')
            ->modalDescription('If you are moving to a new account, you will need to sync all the data to the new service to minimize disruption. Advising App can do this for you, but if you just want to save the settings and do it yourself, you can choose to do so.')
            ->modalWidth(MaxWidth::TwoExtraLarge)
            ->modalSubmitActionLabel('Save and sync all chats')
            ->modalHidden(function (AiIntegrationsSettings $originalSettings) {
                $newSettings = $this->form->getRawState();

                if ($originalSettings->open_ai_gpt_4o_base_uri !== $newSettings['open_ai_gpt_4o_base_uri']) {
                    return false;
                }

                if ($originalSettings->open_ai_gpt_4o_mini_base_uri !== $newSettings['open_ai_gpt_4o_mini_base_uri']) {
                    return false;
                }

                if ($originalSettings->open_ai_gpt_o1_mini_base_uri !== $newSettings['open_ai_gpt_o1_mini_base_uri']) {
                    return false;
                }

                if ($originalSettings->open_ai_gpt_o3_base_uri !== ($newSettings['open_ai_gpt_o3_base_uri'] ?? null)) {
                    return false;
                }

                if ($originalSettings->open_ai_gpt_o3_mini_base_uri !== $newSettings['open_ai_gpt_o3_mini_base_uri']) {
                    return false;
                }

                if ($originalSettings->open_ai_gpt_41_mini_base_uri !== $newSettings['open_ai_gpt_41_mini_base_uri']) {
                    return false;
                }

                if ($originalSettings->open_ai_gpt_41_nano_base_uri !== $newSettings['open_ai_gpt_41_nano_base_uri']) {
                    return false;
                }

                if ($originalSettings->open_ai_gpt_o4_mini_base_uri !== $newSettings['open_ai_gpt_o4_mini_base_uri']) {
                    return false;
                }

                return true;
            })
            ->extraModalFooterActions([
                Action::make('justSave')
                    ->label('Just save the settings')
                    ->color('gray')
                    ->action(fn () => $this->save())
                    ->cancelParentActions(),
            ])
            ->action(function (AiIntegrationsSettings $originalSettings, ResetAiServiceIdsForModel $resetAiServiceIds) {
                $newSettings = $this->form->getState();

                $changedModels = [
                    ...(($originalSettings->open_ai_gpt_4o_base_uri !== $newSettings['open_ai_gpt_4o_base_uri']) ? [AiModel::OpenAiGpt4o] : []),
                    ...(($originalSettings->open_ai_gpt_4o_mini_base_uri !== $newSettings['open_ai_gpt_4o_mini_base_uri']) ? [AiModel::OpenAiGpt4o] : []),
                    ...(($originalSettings->open_ai_gpt_o1_mini_base_uri !== $newSettings['open_ai_gpt_o1_mini_base_uri']) ? [AiModel::OpenAiGptO1Mini] : []),
                    ...(($originalSettings->open_ai_gpt_o3_base_uri !== ($newSettings['open_ai_gpt_o3_base_uri'] ?? null)) ? [AiModel::OpenAiGptO3] : []),
                    ...(($originalSettings->open_ai_gpt_o3_mini_base_uri !== $newSettings['open_ai_gpt_o3_mini_base_uri']) ? [AiModel::OpenAiGptO3Mini] : []),
                    ...(($originalSettings->open_ai_gpt_41_mini_base_uri !== $newSettings['open_ai_gpt_41_mini_base_uri']) ? [AiModel::OpenAiGpt41Mini] : []),
                    ...(($originalSettings->open_ai_gpt_41_nano_base_uri !== $newSettings['open_ai_gpt_41_nano_base_uri']) ? [AiModel::OpenAiGpt41Nano] : []),
                    ...(($originalSettings->open_ai_gpt_o4_mini_base_uri !== $newSettings['open_ai_gpt_o4_mini_base_uri']) ? [AiModel::OpenAiGptO4Mini] : []),
                ];

                DB::transaction(function () use ($changedModels, $resetAiServiceIds) {
                    foreach ($changedModels as $changedModel) {
                        $resetAiServiceIds($changedModel);
                    }
                });

                $this->save();

                foreach ($changedModels as $changedModel) {
                    dispatch(app(ReInitializeAiModel::class, ['model' => $changedModel->value]));
                }
            });
    }
}
