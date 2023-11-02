<?php

namespace Assist\MeetingCenter\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Assist\MeetingCenter\Models\CalendarEvent;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages\EditCalendarEvent;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages\ViewCalendarEvent;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages\ListCalendarEvents;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages\CreateCalendarEvent;

class CalendarEventResource extends Resource
{
    protected static ?string $model = CalendarEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Meeting Center';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Events';

    protected static ?string $modelLabel = 'Event';

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //         ]);
    // }
    //

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalendarEvents::route('/'),
            // 'create' => CreateCalendarEvent::route('/create'),
            'view' => ViewCalendarEvent::route('/{record}'),
            // 'edit' => EditCalendarEvent::route('/{record}/edit'),
        ];
    }
}
