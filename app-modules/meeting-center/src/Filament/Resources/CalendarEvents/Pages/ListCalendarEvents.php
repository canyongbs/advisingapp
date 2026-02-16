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

namespace AdvisingApp\MeetingCenter\Filament\Resources\CalendarEvents\Pages;

use AdvisingApp\MeetingCenter\Filament\Resources\CalendarEvents\CalendarEventResource;
use AdvisingApp\MeetingCenter\Jobs\SyncCalendar;
use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListCalendarEvents extends ListRecords
{
    protected static string $resource = CalendarEventResource::class;

    protected ?string $heading = 'My Appointments';

    protected string $view = 'meeting-center::filament.pages.list-calendar-events';

    #[Url(as: 'view')]
    public string $viewType = 'table';

    //TODO: Look into consolidating to not use an action
    public function setupCalendarProviderAction(): Action
    {
        return Action::make('setupCalendarProviderAction')
            ->modalHeading('Choose a provider')
            ->modalDescription('This feature requires synchronization with Google Calendar or Outlook Calendar. Please select the service you would like to connect to by selecting the appropriate icon below.')
            ->modalContent(view('meeting-center::filament.components.calendar-setup-modal', ['calendar' => auth()->user()->calendar]))
            ->modalSubmitAction(false)
            ->modalCancelAction(Action::make('cancel')->color('gray')->url(Filament::getUrl()))
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false);
    }

    //TODO: Look into consolidating to not use an action
    public function selectCalendarAction(): Action
    {
        return Action::make('selectCalendarAction')
            ->modalHeading('Select a Calendar')
            ->schema([
                Select::make('provider_id')
                    ->hiddenLabel()
                    ->options(function () {
                        /** @var User $user */
                        $user = auth()->user();

                        $calendar = $user->calendar;

                        /** @var CalendarInterface $calendarManager */
                        $calendarManager = resolve(CalendarManager::class)
                            ->driver($calendar->provider_type->value);

                        return $calendarManager->getCalendars($calendar);
                    })
                    ->required(),
            ])
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->modalCancelAction(Action::make('cancel')->color('gray')->url(Filament::getUrl()))
            ->action(function (array $data): void {
                /** @var User $user */
                $user = auth()->user();

                $calendar = $user->calendar;
                $calendar->provider_id = $data['provider_id'];

                $calendars = resolve(CalendarManager::class)
                    ->driver($calendar->provider_type->value)
                    ->getCalendars($calendar);

                $calendar->name = $calendars[$data['provider_id']];

                $calendar->saveQuietly();

                dispatch(new SyncCalendar($calendar));
            });
    }

    public function setViewType(string $viewType): void
    {
        $this->viewType = $viewType;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('title')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime(timezone: auth()->user()->timezone ?? config('app.timezone'))
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime(timezone: auth()->user()->timezone ?? config('app.timezone'))
                    ->sortable(),
                TextColumn::make('attendees')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('pastEvents')
                    ->label('Hide Past Events')
                    ->query(fn (Builder $query): Builder => $query->where('starts_at', '>=', now()->startOfDay()))
                    ->default(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query): Builder {
                /** @var User $user */
                $user = auth()->user();

                return $query->whereRelation('calendar', 'user_id', $user->id);
            })
            ->defaultSort('starts_at')
            ->paginationMode(PaginationMode::Cursor)
            ->poll('10s');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('Sync')
                ->action(function () {
                    /** @var User $user */
                    $user = auth()->user();

                    dispatch(new SyncCalendar($user->calendar));

                    Notification::make()
                        ->title('Calendar synchronization started')
                        ->body('Your calendar is being synchronized in the background. This may take a few moments.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
