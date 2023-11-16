<?php

namespace Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Livewire\Attributes\Url;
use Filament\Facades\Filament;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Columns\OpenSearch\TextColumn;
use Assist\MeetingCenter\Managers\CalendarManager;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;

class ListCalendarEvents extends ListRecords
{
    protected static string $resource = CalendarEventResource::class;

    protected static string $view = 'meeting-center::filament.pages.list-events';

    #[Url(as: 'view')]
    public string $viewType = 'table';

    //TODO: Look into consolidating to not use an action
    public function setupCalendarProviderAction(): Action
    {
        return Action::make('setupCalendarProviderAction')
            ->modalHeading('Choose a provider')
            ->modalDescription('This feature requires synchronization with Google or Outlook. Please select the service you would like
        to synchronize with below to continue.')
            ->modalContent(view('meeting-center::filament.components.calendar-setup-modal'))
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
            ->form([
                Select::make('provider_id')
                    ->hiddenLabel()
                    ->options(function () {
                        /** @var User $user */
                        $user = auth()->user();

                        $calendar = $user->calendar;

                        return resolve(CalendarManager::class)
                            ->driver($calendar->provider_type->value)
                            ->getCalendars($calendar);
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

                //TODO: queue
                resolve(CalendarManager::class)
                    ->driver($calendar->provider_type->value)
                    ->syncEvents($calendar);
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
                    ->sortable(),
                TextColumn::make('ends_at')
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
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('starts_at'));
    }

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
            Action::make('Sync')
                ->action(function () {
                    /** @var User $user */
                    $user = auth()->user();

                    resolve(CalendarManager::class)
                        ->driver($user->calendar->provider_type->value)
                        ->syncEvents($user->calendar);

                    $this->dispatch('refresh-events');
                }),
        ];
    }
}
