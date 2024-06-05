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
use Filament\Pages\SettingsPage;
use AdvisingApp\Ai\Enums\AiModel;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Filament\Support\Enums\MaxWidth;
use AdvisingApp\Ai\Models\AiAssistant;
use Filament\Forms\Components\Section;
use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Ai\Settings\AiSettings;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Ai\Actions\ResetAiServiceIds;
use AdvisingApp\Ai\Jobs\ReInitializeAiService;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Filament\Clusters\ArtificialIntelligence;
use AdvisingApp\Ai\Filament\Resources\AiAssistantResource\Forms\AiAssistantForm;

/**
 * @property-read ?AiAssistant $defaultAssistant

 */
class ManageAiSettings extends SettingsPage
{
    protected static string $settings = AiSettings::class;

    protected static ?string $title = 'Institutional Assistant';

    protected static ?string $cluster = ArtificialIntelligence::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasLicense(LicenseType::ConversationalAi)) {
            return false;
        }

        return $user->can(['assistant.access_ai_settings']);
    }

    #[Computed]
    public function defaultAssistant(): ?AiAssistant
    {
        return AiAssistant::query()
            ->where('application', AiApplication::PersonalAssistant)
            ->where('is_default', true)
            ->first();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                resolve(AiAssistantForm::class)->form(
                    Section::make('Default assistant')
                        ->statePath('defaultAssistant')
                        ->columns(2)
                        ->visible($this->defaultAssistant !== null)
                        ->model($this->defaultAssistant),
                ),
                TextInput::make('max_tokens')
                    ->label('Max Tokens')
                    ->required()
                    ->numeric()
                    ->columnSpan('1/2'),
                TextInput::make('temperature')
                    ->label('Temperature')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->step(0.1)
                    ->minValue(0.0)
                    ->maxValue(2.0)
                    ->columnSpan('1/2'),
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
            ->modalHidden(function () {
                $newModelValue = $this->form->getRawState()['defaultAssistant']['model'] ?? null;

                if (blank($newModelValue)) {
                    return true;
                }

                $newModel = AiModel::parse($newModelValue);

                return $this->defaultAssistant->model->isSharedDeployment($newModel);
            })
            ->extraModalFooterActions([
                Action::make('justSave')
                    ->label('Just save the settings')
                    ->color('gray')
                    ->action(fn () => $this->save())
                    ->cancelParentActions(),
            ])
            ->action(function (ResetAiServiceIds $resetAiServiceIds) {
                $newModelValue = $this->form->getRawState()['defaultAssistant']['model'] ?? null;
                $newModel = filled($newModelValue) ? AiModel::parse($newModelValue) : null;

                $modelDeploymentIsShared = $newModel ? $this->defaultAssistant->model->isSharedDeployment($newModel) : true;

                if (! $modelDeploymentIsShared) {
                    DB::transaction(function () use ($resetAiServiceIds) {
                        $resetAiServiceIds($this->defaultAssistant?->model);
                    });
                }

                $this->save();

                if (! $modelDeploymentIsShared) {
                    Bus::batch([
                        app(ReInitializeAiService::class, ['model' => $newModel->value]),
                    ])->dispatch();
                }
            });
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['defaultAssistant'] = $this->defaultAssistant?->attributesToArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('defaultAssistant', $data)) {
            $this->defaultAssistant->update($data['defaultAssistant']);

            unset($data['defaultAssistant']);
        }

        return parent::mutateFormDataBeforeSave($data);
    }
}
