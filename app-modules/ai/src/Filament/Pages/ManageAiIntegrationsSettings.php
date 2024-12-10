<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use Filament\Forms\Form;
use Filament\Actions\Action;
use App\Models\Authenticatable;
use Filament\Pages\SettingsPage;
use AdvisingApp\Ai\Enums\AiModel;
use Illuminate\Support\Facades\DB;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Ai\Jobs\ReInitializeAiModel;
use App\Filament\Clusters\ProductIntegrations;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\Ai\Actions\ResetAiServiceIdsForModel;

class ManageAiIntegrationsSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = AiIntegrationsSettings::class;

    protected static ?string $title = 'Cognitive Services Settings';

    protected static ?string $navigationLabel = 'Cognitive Services';

    protected static ?int $navigationSort = 100;

    protected static ?string $cluster = ProductIntegrations::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->hasRole(Authenticatable::SUPER_ADMIN_ROLE);
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Section::make('Azure OpenAI')
                    ->collapsible()
                    ->schema([
                        Section::make('GPT 3.5')
                            ->collapsible()
                            ->schema([
                                TextInput::make('open_ai_gpt_35_base_uri')
                                    ->label('Base URI')
                                    ->placeholder('https://example.openai.azure.com/openai')
                                    ->url(),
                                TextInput::make('open_ai_gpt_35_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                TextInput::make('open_ai_gpt_35_model')
                                    ->label('Model'),
                            ]),
                        Section::make('GPT 4')
                            ->collapsible()
                            ->schema([
                                TextInput::make('open_ai_gpt_4_base_uri')
                                    ->label('Base URI')
                                    ->placeholder('https://example.openai.azure.com/openai')
                                    ->url(),
                                TextInput::make('open_ai_gpt_4_api_key')
                                    ->label('API Key')
                                    ->password()
                                    ->autocomplete(false),
                                TextInput::make('open_ai_gpt_4_model')
                                    ->label('Model'),
                            ]),
                        Section::make('GPT 4o')
                            ->collapsible()
                            ->schema([
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
                            ]),
                        Section::make('GPT 4o mini')
                            ->collapsible()
                            ->schema([
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

                if ($originalSettings->open_ai_gpt_35_base_uri !== $newSettings['open_ai_gpt_35_base_uri']) {
                    return false;
                }

                if ($originalSettings->open_ai_gpt_4_base_uri !== $newSettings['open_ai_gpt_4_base_uri']) {
                    return false;
                }

                if ($originalSettings->open_ai_gpt_4o_base_uri !== $newSettings['open_ai_gpt_4o_base_uri']) {
                    return false;
                }

                if ($originalSettings->open_ai_gpt_4o_mini_base_uri !== $newSettings['open_ai_gpt_4o_mini_base_uri']) {
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
                    ...(($originalSettings->open_ai_gpt_35_base_uri !== $newSettings['open_ai_gpt_35_base_uri']) ? [AiModel::OpenAiGpt35] : []),
                    ...(($originalSettings->open_ai_gpt_4_base_uri !== $newSettings['open_ai_gpt_4_base_uri']) ? [AiModel::OpenAiGpt4] : []),
                    ...(($originalSettings->open_ai_gpt_4o_base_uri !== $newSettings['open_ai_gpt_4o_base_uri']) ? [AiModel::OpenAiGpt4o] : []),
                    ...(($originalSettings->open_ai_gpt_4o_mini_base_uri !== $newSettings['open_ai_gpt_4o_mini_base_uri']) ? [AiModel::OpenAiGpt4o] : []),
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
