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

namespace AdvisingApp\Application\Filament\Resources\Applications\Pages;

use AdvisingApp\Application\Actions\CreateApplicationVersion;
use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\Concerns\HasSharedFormConfiguration;
use AdvisingApp\Application\Filament\Resources\Applications\Schemas\ApplicationInfolist;
use AdvisingApp\Application\Filament\Resources\Applications\Schemas\ApplicationNotificationsForm;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationStep;
use AdvisingApp\Form\Actions\GenerateSubmissibleEmbedCode;
use AdvisingApp\Form\Actions\SaveSubmissibleFieldsFromContent;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

/**
 * @property-read Schema $infolist
 * @property-read Schema $notificationsForm
 */
class ManageApplication extends EditRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = ApplicationResource::class;

    #[Url(as: 'tab')]
    public ?string $activeRelationManager = null;

    /** @var array<string, mixed>|null */
    public ?array $infolistData = [];

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

        return [
            '' => Tab::make('View')
                ->visible(fn (): bool => ApplicationResource::canView($record))
                ->schema([
                    EmbeddedSchema::make('infolist'),
                ]),
            'edit' => $this->getContentTabComponent()
                ->label('Edit')
                ->visible(fn (): bool => ApplicationResource::canEdit($record)),
            'workflows' => $this->getRelationManagerTabComponent('workflows'),
            'submissions' => $this->getRelationManagerTabComponent('submissions'),
            'notifications' => Tab::make('Notifications')
                ->visible(fn (): bool => ApplicationResource::canEdit($record))
                ->schema([
                    $this->makeFormContentComponent('notificationsForm', 'saveNotifications'),
                ]),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return ApplicationInfolist::configure($schema)
            ->model($this->getRecord())
            ->operation('view')
            ->statePath('infolistData');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->fields());
    }

    public function notificationsForm(Schema $schema): Schema
    {
        return ApplicationNotificationsForm::configure($schema)
            ->model($this->getRecord())
            ->operation('edit')
            ->statePath('notificationsData');
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        ApplicationResource::authorizeEdit($this->getRecord());

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    public function saveFormComponentOnly(Component $component): void
    {
        ApplicationResource::authorizeEdit($this->getRecord());

        parent::saveFormComponentOnly($component);
    }

    public function saveNotifications(): void
    {
        $record = $this->getRecord();

        ApplicationResource::authorizeEdit($record);

        DB::transaction(fn () => $record->update($this->notificationsForm->getState()));

        $this->getSavedNotification()?->send();
    }

    protected function authorizeAccess(): void
    {
        $record = $this->getRecord();

        abort_unless(
            ApplicationResource::canView($record)
                || ApplicationResource::canEdit($record)
                || filled($this->getCachedRelationManagers()),
            403,
        );
    }

    protected function fillForm(): void
    {
        $record = $this->getRecord();
        $data = $record->attributesToArray();

        if (ApplicationResource::canView($record)) {
            $this->infolist->fill($data);
        }

        if (ApplicationResource::canEdit($record)) {
            parent::fillForm();

            $this->notificationsForm->fill($data);
        }
    }

    protected function makeFormContentComponent(string $schema, string $submitHandler): Component
    {
        return Form::make([EmbeddedSchema::make($schema)])
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
                ->url(fn (Application $application) => route('applications.preview', $application))
                ->openUrlInNewTab(),
            Action::make('view')
                ->url(fn (Application $application) => route('applications.show', ['application' => $application]))
                ->icon(Heroicon::ArrowTopRightOnSquare)
                ->openUrlInNewTab(),
            Action::make('embed_snippet')
                ->label('Embed Snippet')
                ->schema([
                    TextEntry::make('snippet')
                        ->label('Click to Copy')
                        ->state(function (Application $application) {
                            $code = resolve(GenerateSubmissibleEmbedCode::class)->handle($application);

                            $state = <<<EOD
                            ```
                            {$code}
                            ```
                            EOD;

                            return str($state)->markdown()->toHtmlString();
                        })
                        ->copyable()
                        ->copyableState(fn (Application $application) => resolve(GenerateSubmissibleEmbedCode::class)->handle($application))
                        ->copyMessage('Copied!')
                        ->copyMessageDuration(1500)
                        ->extraAttributes(['class' => 'embed-code-snippet']),
                ])
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->hidden(fn (Application $application) => ! $application->embed_enabled),
            ArchiveAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Save'),
            $this->getCancelFormAction()
                ->url(fn () => ApplicationResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return ApplicationResource::getUrl('view', [
            'record' => $this->getRecord(),
            'tab' => 'edit',
        ]);
    }

    protected function beforeSave(): void
    {
        $this->versioningFormData = $this->data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        assert($record instanceof Application);

        return DB::transaction(function () use ($record, $data) {
            $newVersion = app(CreateApplicationVersion::class)->execute($record, $data);

            $this->record = $newVersion;

            app(SaveSubmissibleFieldsFromContent::class)->execute($newVersion, $this->versioningFormData);

            $this->copyMedia($record, $newVersion);

            $this->saveRelationshipsToNewVersion($newVersion);

            return $newVersion;
        });
    }

    /** Validation saved the relationships to the previous version, so save them again to the new one. */
    protected function saveRelationshipsToNewVersion(Application $newVersion): void
    {
        $this->data = $this->versioningFormData ?? [];
        $this->data['content'] = $newVersion->content;

        if ($newVersion->is_wizard) {
            $oldStepKeys = array_keys($this->data['steps'] ?? []);
            $oldAttachments = $this->componentFileAttachments['data']['steps'] ?? [];
            $this->data['steps'] = [];
            $this->componentFileAttachments['data']['steps'] = [];

            foreach ($newVersion->steps->sortBy('sort')->values() as $index => $step) {
                assert($step instanceof ApplicationStep);
                $newKey = "record-{$step->getKey()}";
                $this->data['steps'][$newKey] = $step->attributesToArray();
                $oldKey = $oldStepKeys[$index] ?? null;

                if ($oldKey !== null && isset($oldAttachments[$oldKey])) {
                    $this->componentFileAttachments['data']['steps'][$newKey] = $oldAttachments[$oldKey];
                }
            }

            $repeater = $this->form->getComponent(
                fn (Component $component): bool => $component instanceof Repeater && $component->getName() === 'steps',
                withActions: false,
                withHidden: true,
            );
            assert($repeater instanceof Repeater);
            $repeater->clearCachedExistingRecords();
        }

        $this->form->model($newVersion)->saveRelationships();
    }

    private function copyMedia(Application $oldVersion, Application $newVersion): void
    {
        if ($newVersion->is_wizard) {
            $this->copyStepMedia($oldVersion, $newVersion);
        } else {
            $this->copyApplicationMedia($oldVersion, $newVersion);
        }
    }

    private function copyApplicationMedia(Application $oldVersion, Application $newVersion): void
    {
        $media = $oldVersion->getMedia('content');

        if ($media->isEmpty()) {
            return;
        }

        $uuidMap = [];

        foreach ($media as $item) {
            $newMedia = $item->copy($newVersion, 'content', 's3-public');
            $uuidMap[$item->uuid] = $newMedia->uuid;
        }

        $newVersion->content = $this->remapMediaUuids($newVersion->content, $uuidMap);
        $newVersion->save();
    }

    private function copyStepMedia(Application $oldVersion, Application $newVersion): void
    {
        $oldSteps = $oldVersion->steps()->get()->keyBy('id');
        $newSteps = $newVersion->steps->sortBy('sort')->values();
        $submittedStepKeys = array_keys($this->versioningFormData['steps'] ?? []);

        foreach ($newSteps as $index => $newStep) {
            $submittedKey = $submittedStepKeys[$index] ?? null;
            $oldStep = is_string($submittedKey) && str_starts_with($submittedKey, 'record-')
                ? $oldSteps->get(substr($submittedKey, strlen('record-')))
                : null;

            if (! $oldStep) {
                continue;
            }

            assert($newStep instanceof ApplicationStep);

            $media = $oldStep->getMedia('content');

            if ($media->isEmpty()) {
                continue;
            }

            $uuidMap = [];

            foreach ($media as $item) {
                $newMedia = $item->copy($newStep, 'content', 's3-public');
                $uuidMap[$item->uuid] = $newMedia->uuid;
            }

            $newStep->content = $this->remapMediaUuids($newStep->content, $uuidMap);
            $newStep->save();
        }
    }

    /**
     * @param array<string, mixed>|null $content
     * @param array<string, string> $uuidMap
     *
     * @return array<string, mixed>|null
     */
    private function remapMediaUuids(?array $content, array $uuidMap): ?array
    {
        if (! $content) {
            return $content;
        }

        $json = json_encode($content);
        $json = str_replace(array_keys($uuidMap), array_values($uuidMap), $json);

        return json_decode($json, true);
    }
}
