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

namespace AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages;

use AdvisingApp\MeetingCenter\Filament\Resources\Events\EventResource;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\Concerns\HasEventRegistrationForm;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Schemas\EventDetailsForm;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Schemas\EventInfolist;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Schemas\EventLandingPageForm;
use AdvisingApp\MeetingCenter\Models\Event;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

/**
 * @property-read Schema $landingPageForm
 * @property-read Schema $registrationForm
 */
class ManageEvent extends EditRecord
{
    use HasEventRegistrationForm;

    protected static string $resource = EventResource::class;

    #[Url(as: 'tab')]
    public ?string $activeRelationManager = null;

    /** @var array<string, mixed>|null */
    public ?array $landingPageData = [];

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
            '' => Tab::make('Overview')
                ->visible(fn (): bool => EventResource::canView($record))
                ->schema([
                    EmbeddedSchema::make('infolist'),
                ]),
            'details' => $this->getContentTabComponent()
                ->label('Details')
                ->visible(fn (): bool => EventResource::canEdit($record)),
            'landing-page' => Tab::make('Landing Page')
                ->visible(fn (): bool => EventResource::canEdit($record))
                ->schema([
                    $this->makeFormContentComponent('landingPageForm', 'saveLandingPage'),
                ]),
            'registration-form' => Tab::make('Registration Form')
                ->visible(fn (): bool => EventResource::canEdit($record))
                ->schema([
                    $this->makeFormContentComponent('registrationForm', 'saveRegistrationForm'),
                ]),
            'attendees' => $this->getRelationManagerTabComponent('attendees'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return EventInfolist::configure($schema)
            ->record($this->getRecord());
    }

    public function form(Schema $schema): Schema
    {
        return EventDetailsForm::configure($schema);
    }

    public function landingPageForm(Schema $schema): Schema
    {
        return EventLandingPageForm::configure($schema)
            ->model($this->getRecord())
            ->operation('edit')
            ->statePath('landingPageData');
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        EventResource::authorizeEdit($this->getRecord());

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    public function saveFormComponentOnly(Component $component): void
    {
        EventResource::authorizeEdit($this->getRecord());

        parent::saveFormComponentOnly($component);
    }

    public function saveLandingPage(): void
    {
        $record = $this->getRecord();

        EventResource::authorizeEdit($record);

        DB::transaction(fn () => $record->update($this->landingPageForm->getState()));

        $this->getSavedNotification()?->send();
    }

    protected function authorizeAccess(): void
    {
        $record = $this->getRecord();

        abort_unless(
            EventResource::canView($record)
                || EventResource::canEdit($record)
                || filled($this->getCachedRelationManagers()),
            403,
        );
    }

    protected function fillForm(): void
    {
        $record = $this->getRecord();

        if (! EventResource::canEdit($record)) {
            return;
        }

        $data = $record->attributesToArray();

        parent::fillForm();

        $this->landingPageForm->fill($data);
        $this->registrationForm->fill($data);
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
            Action::make('view')
                ->url(fn (Event $record): string => route('event-registration.show', ['event' => $record]))
                ->icon(Heroicon::ArrowTopRightOnSquare)
                ->openUrlInNewTab(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Save'),
            ArchiveAction::make(),
            $this->getCancelFormAction()
                ->url(fn (): string => EventResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return EventResource::getUrl('view', [
            'record' => $this->getRecord(),
            'tab' => 'details',
        ]);
    }
}
