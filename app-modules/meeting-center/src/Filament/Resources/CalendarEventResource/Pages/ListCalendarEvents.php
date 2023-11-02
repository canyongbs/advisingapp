<?php

namespace Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Columns\OpenSearch\TextColumn;
use Assist\MeetingCenter\Managers\CalendarManager;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;

class ListCalendarEvents extends ListRecords
{
    protected static string $resource = CalendarEventResource::class;

    protected static string $view = 'meeting-center::filament.pages.list-events';

    public string $viewType = 'table';

    public function setupCalendarProviderAction(): Action
    {
        return Action::make('setupCalendarProviderAction')
            ->modalHeading('Choose a provider')
            ->modalDescription('This feature requires synchronization with Google or Outlook. Please select the service you would like
        to synchronize with below to continue.')
            ->modalContent(view('meeting-center::filament.components.calendar-setup-modal'))
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false);
    }

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
                            ->driver($calendar->type)
                            ->getCalendars($calendar);
                    })
                    ->required(),
            ])
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->action(function (array $data): void {
                /** @var User $user */
                $user = auth()->user();

                $calendar = $user->calendar;
                $calendar->provider_id = $data['provider_id'];
                $calendar->saveQuietly();

                resolve(CalendarManager::class)
                    ->driver($calendar->type)
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
                TextColumn::make('title'),
                TextColumn::make('starts_at'),
                TextColumn::make('ends_at'),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
