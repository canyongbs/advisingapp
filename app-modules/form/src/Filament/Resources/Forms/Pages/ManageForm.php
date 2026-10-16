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
use AdvisingApp\Form\Actions\GenerateSubmissibleEmbedCode;
use AdvisingApp\Form\Actions\SaveSubmissibleFieldsFromContent;
use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\Concerns\HasSharedFormConfiguration;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\Concerns\ValidatesProspectGenerationFields;
use AdvisingApp\Form\Filament\Resources\Forms\Schemas\FormEmailAutoReplyForm;
use AdvisingApp\Form\Filament\Resources\Forms\Schemas\FormInfolist;
use AdvisingApp\Form\Filament\Resources\Forms\Schemas\FormNotificationsForm;
use AdvisingApp\Form\Filament\Resources\Forms\Schemas\FormOnScreenResponseForm;
use AdvisingApp\Form\Models\Form;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form as FormComponent;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

/**
 * @property-read Schema $infolist
 * @property-read Schema $onScreenResponseForm
 * @property-read Schema $emailAutoReplyForm
 * @property-read Schema $notificationsForm
 */
class ManageForm extends EditRecord
{
    use HasSharedFormConfiguration;
    use ValidatesProspectGenerationFields;

    protected static string $resource = FormResource::class;

    #[Url(as: 'tab')]
    public ?string $activeRelationManager = null;

    /** @var array<string, mixed>|null */
    public ?array $infolistData = [];

    /** @var array<string, mixed>|null */
    public ?array $onScreenResponseData = [];

    /** @var array<string, mixed>|null */
    public ?array $emailAutoReplyData = [];

    /** @var array<string, mixed>|null */
    public ?array $notificationsData = [];

    /** @var array<string, mixed>|null */
    protected ?array $versioningFormData = null;

    public function getTitle(): string
    {
        return __('filament-panels::resources/pages/view-record.title', ['label' => $this->getRecordTitle()]);
    }

    public function getBreadcrumb(): string
    {
        return __('filament-panels::resources/pages/view-record.breadcrumb');
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getRelationManagersContentComponent(): Component
    {
        $tabs = parent::getRelationManagersContentComponent();
        assert($tabs instanceof Tabs);

        return $tabs->contained();
    }

    public function getTabComponents(): array
    {
        $record = $this->getRecord();
        assert($record instanceof Form);

        return [
            '' => Tab::make('View')
                ->visible(fn (): bool => FormResource::canView($record))
                ->schema([
                    EmbeddedSchema::make('infolist'),
                ]),
            'edit' => $this->getContentTabComponent()
                ->label('Edit')
                ->visible(fn (): bool => FormResource::canEdit($record)),
            'on-screen-response' => Tab::make('On-Screen Response')
                ->visible(fn (): bool => FormResource::canEdit($record))
                ->schema([
                    $this->makeFormContentComponent('onScreenResponseForm', 'saveOnScreenResponse'),
                ]),
            'workflows' => $this->getRelationManagerTabComponent('workflows'),
            'submissions' => $this->getRelationManagerTabComponent('submissions'),
            'email-auto-reply' => Tab::make('Email Auto Reply')
                ->visible(fn (): bool => FormResource::canEdit($record) && $record->is_authenticated)
                ->schema([
                    $this->makeFormContentComponent('emailAutoReplyForm', 'saveEmailAutoReply'),
                ]),
            'notifications' => Tab::make('Notifications')
                ->visible(fn (): bool => FormResource::canEdit($record))
                ->schema([
                    $this->makeFormContentComponent('notificationsForm', 'saveNotifications'),
                ]),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return FormInfolist::configure($schema)
            ->model($this->getRecord())
            ->operation('view')
            ->statePath('infolistData');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->fields());
    }

    public function onScreenResponseForm(Schema $schema): Schema
    {
        return FormOnScreenResponseForm::configure($schema)
            ->model($this->getRecord())
            ->operation('edit')
            ->statePath('onScreenResponseData');
    }

    public function emailAutoReplyForm(Schema $schema): Schema
    {
        return FormEmailAutoReplyForm::configure($schema)
            ->model($this->getRecord())
            ->operation('edit')
            ->statePath('emailAutoReplyData');
    }

    public function notificationsForm(Schema $schema): Schema
    {
        return FormNotificationsForm::configure($schema)
            ->model($this->getRecord())
            ->operation('edit')
            ->statePath('notificationsData');
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        FormResource::authorizeEdit($this->getRecord());

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    public function saveFormComponentOnly(Component $component): void
    {
        FormResource::authorizeEdit($this->getRecord());

        parent::saveFormComponentOnly($component);
    }

    public function saveOnScreenResponse(): void
    {
        $record = $this->getRecord();

        FormResource::authorizeEdit($record);

        DB::transaction(fn () => $record->update($this->onScreenResponseForm->getState()));

        $this->getSavedNotification()?->send();
    }

    public function saveEmailAutoReply(): void
    {
        $record = $this->getRecord();
        assert($record instanceof Form);

        FormResource::authorizeEdit($record);
        abort_unless($record->is_authenticated, 403);

        DB::transaction(function (): void {
            $this->emailAutoReplyForm->getState();
            $this->emailAutoReplyForm->saveRelationships();
        });

        $this->getSavedNotification()?->send();
    }

    public function saveNotifications(): void
    {
        $record = $this->getRecord();

        FormResource::authorizeEdit($record);

        DB::transaction(fn () => $record->update($this->notificationsForm->getState()));

        $this->getSavedNotification()?->send();
    }

    protected function authorizeAccess(): void
    {
        $record = $this->getRecord();

        abort_unless(
            FormResource::canView($record)
                || FormResource::canEdit($record)
                || filled($this->getCachedRelationManagers()),
            403,
        );
    }

    protected function fillForm(): void
    {
        $record = $this->getRecord();
        $data = $record->attributesToArray();

        if (FormResource::canView($record)) {
            $this->infolist->fill($data);
        }

        if (FormResource::canEdit($record)) {
            parent::fillForm();

            $this->onScreenResponseForm->fill($data);
            $this->emailAutoReplyForm->fill($data);
            $this->notificationsForm->fill($data);
        }
    }

    protected function makeFormContentComponent(string $schema, string $submitHandler): Component
    {
        return FormComponent::make([EmbeddedSchema::make($schema)])
            ->id($schema)
            ->livewireSubmitHandler($submitHandler)
            ->footer([
                Actions::make([
                    Action::make($submitHandler)
                        ->label('Save')
                        ->submit($submitHandler),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview')
                ->icon(Heroicon::OutlinedEye)
                ->url(fn (Form $form) => route('forms.preview', $form))
                ->visible(fn (): bool => FormResource::canView($this->getRecord()))
                ->openUrlInNewTab(),
            Action::make('view')
                ->url(fn (Form $form) => route('forms.show', ['form' => $form]))
                ->icon(Heroicon::ArrowTopRightOnSquare)
                ->visible(fn (): bool => FormResource::canView($this->getRecord()))
                ->openUrlInNewTab(),
            Action::make('embed_snippet')
                ->label('Embed Snippet')
                ->schema([
                    TextEntry::make('snippet')
                        ->label('Click to Copy')
                        ->state(function (Form $form) {
                            $code = resolve(GenerateSubmissibleEmbedCode::class)->handle($form);

                            $state = <<<EOD
                            ```
                            {$code}
                            ```
                            EOD;

                            return str($state)->markdown()->toHtmlString();
                        })
                        ->copyable()
                        ->copyableState(fn (Form $form) => resolve(GenerateSubmissibleEmbedCode::class)->handle($form))
                        ->copyMessage('Copied!')
                        ->copyMessageDuration(1500)
                        ->extraAttributes(['class' => 'embed-code-snippet']),
                ])
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->hidden(fn (Form $form) => (! FormResource::canView($form)) || (! $form->embed_enabled)),
            ArchiveAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->versioningFormData = $this->data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        assert($record instanceof Form);

        return DB::transaction(function () use ($record, $data) {
            $newVersion = app(CreateFormVersion::class)->execute($record, $data);

            $this->record = $newVersion;

            app(SaveSubmissibleFieldsFromContent::class)->execute($newVersion, $this->versioningFormData);

            return $newVersion;
        });
    }

    protected function getRedirectUrl(): string
    {
        return FormResource::getUrl('view', [
            'record' => $this->getRecord(),
            'tab' => 'edit',
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Save'),
            $this->getCancelFormAction()
                ->url(fn () => FormResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }
}
