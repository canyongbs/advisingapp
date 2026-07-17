<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Form\Filament\Resources\Forms\Pages;

use AdvisingApp\Form\Actions\CreateFormVersion;
use AdvisingApp\Form\Actions\SaveSubmissibleFieldsFromContent;
use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\Concerns\HasSharedFormConfiguration;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\Concerns\ValidatesProspectGenerationFields;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditForm extends EditRecord
{
    use HasSharedFormConfiguration;
    use ValidatesProspectGenerationFields;

    protected static string $resource = FormResource::class;

    protected static ?string $navigationLabel = 'Edit';

    /** @var array<string, mixed>|null */
    protected ?array $versioningFormData = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->fields());
    }

    protected function beforeSave(): void
    {
        $this->versioningFormData = $this->data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Form $record */
        return DB::transaction(function () use ($record, $data) {
            $newVersion = app(CreateFormVersion::class)->execute($record, $data);

            $this->record = $newVersion;

            app(SaveSubmissibleFieldsFromContent::class)->execute($newVersion, $this->versioningFormData);

            return $newVersion;
        });
    }

    protected function getRedirectUrl(): ?string
    {
        return FormResource::getUrl('view', ['record' => $this->record]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Save')
                ->formId('form'),
            $this->getCancelFormAction()
                ->url(fn () => FormResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Save')
                ->formId('form'),
            $this->getArchiveFormAction(),
            DeleteAction::make()
                ->hidden(fn (): bool => $this->formHasSubmissions()),
            $this->getCancelFormAction()
                ->url(fn () => FormResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    private function getArchiveFormAction(): Action
    {
        return Action::make('archive')
            ->label('Archive')
            ->icon(FilamentIcon::resolve('actions::delete-action') ?? 'heroicon-o-archive-box')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Archive Form')
            ->modalDescription('This form has submissions. Archiving will hide it from active use. This action cannot be undone.')
            ->modalSubmitActionLabel('Archive')
            ->visible(fn (): bool => $this->formHasSubmissions())
            ->action(function (): void {
                /** @var Form $record */
                $record = $this->record;
                $record->archive();

                $this->redirect(FormResource::getUrl('index'));
            });
    }

    private function formHasSubmissions(): bool
    {
        /** @var Form $record */
        $record = $this->record;

        return FormSubmission::query()
            ->whereHas(
                'submissible',
                fn (Builder $query) => $query->withoutGlobalScopes()->where('root_id', $record->root_id),
            )
            ->exists();
    }
}
