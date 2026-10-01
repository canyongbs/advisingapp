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

use AdvisingApp\MeetingCenter\Enums\EventTab;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\EventResource;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\RelationManagers\EventAttendeesRelationManager;
use AdvisingApp\MeetingCenter\Livewire\EventDetailsManager;
use AdvisingApp\MeetingCenter\Livewire\EventLandingPageManager;
use AdvisingApp\MeetingCenter\Livewire\EventRegistrationFormManager;
use AdvisingApp\MeetingCenter\Models\Event;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    #[Url(as: 'tab')]
    public string $activeTab = EventTab::Overview->value;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if ($this->isActiveTabVisible()) {
            return;
        }

        $firstVisibleTab = collect(EventTab::cases())
            ->first(fn (EventTab $tab): bool => $this->isTabVisible($tab));

        abort_unless($firstVisibleTab !== null, 403);

        $this->activeTab = $firstVisibleTab->value;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->livewireProperty('activeTab')
                    ->tabs([
                        EventTab::Overview->value => Tab::make(EventTab::Overview->getLabel())
                            ->visible(fn (): bool => $this->isTabVisible(EventTab::Overview))
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('title'),
                                        TextEntry::make('description')
                                            ->label('Description')
                                            ->state(function (Event $record): string {
                                                if (blank($record->description)) {
                                                    return '-';
                                                }

                                                return $record->getRichContentAttribute('description')?->toHtml() ?? '-';
                                            })
                                            ->html()
                                            ->columnSpanFull(),
                                        TextEntry::make('location'),
                                        TextEntry::make('capacity'),
                                        TextEntry::make('starts_at')
                                            ->dateTime(),
                                        TextEntry::make('ends_at')
                                            ->dateTime(),
                                        TextEntry::make('createdBy.name')
                                            ->label('Created By'),
                                        TextEntry::make('lastUpdatedBy.name')
                                            ->label('Last Updated By'),
                                    ])
                                    ->columns(),
                            ]),
                        EventTab::Details->value => Tab::make(EventTab::Details->getLabel())
                            ->visible(fn (): bool => $this->isTabVisible(EventTab::Details))
                            ->schema([
                                Livewire::make(EventDetailsManager::class, [
                                    'record' => $this->getRecord(),
                                    'lazy' => 'on-load',
                                ])->key('event-details-manager'),
                            ]),
                        EventTab::LandingPage->value => Tab::make(EventTab::LandingPage->getLabel())
                            ->visible(fn (): bool => $this->isTabVisible(EventTab::LandingPage))
                            ->schema([
                                Livewire::make(EventLandingPageManager::class, [
                                    'record' => $this->getRecord(),
                                    'lazy' => 'on-load',
                                ])->key('event-landing-page-manager'),
                            ]),
                        EventTab::RegistrationForm->value => Tab::make(EventTab::RegistrationForm->getLabel())
                            ->visible(fn (): bool => $this->isTabVisible(EventTab::RegistrationForm))
                            ->schema([
                                Livewire::make(EventRegistrationFormManager::class, [
                                    'record' => $this->getRecord(),
                                    'lazy' => 'on-load',
                                ])->key('event-registration-form-manager'),
                            ]),
                        EventTab::Attendees->value => Tab::make(EventTab::Attendees->getLabel())
                            ->visible(fn (): bool => $this->isTabVisible(EventTab::Attendees))
                            ->schema([
                                Livewire::make(EventAttendeesRelationManager::class, [
                                    'ownerRecord' => $this->getRecord(),
                                    'pageClass' => static::class,
                                    'lazy' => 'on-load',
                                ])->key('event-attendees-relation-manager'),
                            ]),
                    ]),
            ]);
    }

    protected function authorizeAccess(): void
    {
        abort_unless(collect(EventTab::cases())->contains(fn (EventTab $tab): bool => $this->isTabVisible($tab)), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->url(fn (Event $record): string => route('event-registration.show', ['event' => $record]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            ArchiveAction::make(),
        ];
    }

    protected function isActiveTabVisible(): bool
    {
        return ($tab = EventTab::tryFrom($this->activeTab)) instanceof EventTab
            && $this->isTabVisible($tab);
    }

    protected function isTabVisible(EventTab $tab): bool
    {
        $record = $this->getRecord();

        return match ($tab) {
            EventTab::Overview => EventResource::canView($record),
            EventTab::Details, EventTab::LandingPage, EventTab::RegistrationForm => EventResource::canEdit($record),
            EventTab::Attendees => EventAttendeesRelationManager::canViewForRecord($record, static::class),
        };
    }
}
