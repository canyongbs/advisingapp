<?php

namespace Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;

class ListCalendarEvents extends ListRecords
{
    protected static string $resource = CalendarEventResource::class;

    protected static string $view = 'meeting-center::filament.pages.list-events';

    public function calendarSetupAction(): Action
    {
        return Action::make('calendarSetup')
            ->modalHeading('Choose a provider')
            ->modalDescription('This feature requires synchronization with Google or Outlook. Please select the service you would like
        to synchronize with below to continue.')
            ->modalContent(view('meeting-center::filament.components.calendar-setup-modal'))
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
            ])
            ->filters([
            ])
            ->actions([
                // ViewAction::make(),
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
