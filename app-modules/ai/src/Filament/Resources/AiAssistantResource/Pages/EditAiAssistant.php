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

namespace AdvisingApp\Ai\Filament\Resources\AiAssistantResource\Pages;

use Throwable;
use Filament\Forms\Form;
use Filament\Actions\Action;
use AdvisingApp\Ai\Enums\AiModel;
use Illuminate\Support\Facades\DB;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Ai\Jobs\ReInitializeAiService;
use AdvisingApp\Ai\Actions\ResetAiServiceIdsForAssistant;
use AdvisingApp\Ai\Filament\Resources\AiAssistantResource;
use AdvisingApp\Ai\Filament\Resources\AiAssistantResource\Forms\AiAssistantForm;

class EditAiAssistant extends EditRecord
{
    protected static string $resource = AiAssistantResource::class;

    public function form(Form $form): Form
    {
        return resolve(AiAssistantForm::class)->form($form);
    }

    public function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->modalHeading('Sync all chats to this new service?')
            ->modalDescription('If you are moving to a new account, you will need to sync all the data to the new service to minimize disruption. Advising App can do this for you, but if you just want to save the settings and do it yourself, you can choose to do so.')
            ->modalWidth(MaxWidth::ThreeExtraLarge)
            ->modalSubmitActionLabel('Save and sync all chats')
            ->modalHidden(function () {
                $newModel = AiModel::parse($this->form->getRawState()['model']);

                return $this->getRecord()->model->isSharedDeployment($newModel);
            })
            ->extraModalFooterActions([
                Action::make('justSave')
                    ->label('Just save the settings')
                    ->color('gray')
                    ->action(fn () => $this->save())
                    ->cancelParentActions(),
            ])
            ->action(function (ResetAiServiceIdsForAssistant $resetAiServiceIds) {
                $newModel = AiModel::parse($this->form->getState()['model']);

                $modelDeploymentIsShared = $this->getRecord()->model->isSharedDeployment($newModel);

                if (! $modelDeploymentIsShared) {
                    DB::transaction(function () use ($resetAiServiceIds) {
                        $resetAiServiceIds($this->getRecord());
                    });
                }

                $this->save();

                if (! $modelDeploymentIsShared) {
                    ReInitializeAiService::dispatchForAssistant($this->getRecord());
                }
            });
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill($data);

        $aiService = $record->model->getService();

        try {
            $aiService->isAssistantExisting($record) ?
                $aiService->updateAssistant($record) :
                $aiService->createAssistant($record);
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Could not save assistant')
                ->body('We failed to connect to the AI service. Support has been notified about this problem. Please try again later.')
                ->danger()
                ->send();

            $this->halt();
        }

        $record->save();

        return $record;
    }
}
