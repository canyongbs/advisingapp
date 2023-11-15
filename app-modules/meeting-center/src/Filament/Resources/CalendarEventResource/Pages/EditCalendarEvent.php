<?php

namespace Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DateTimePicker;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;

class EditCalendarEvent extends EditRecord
{
    protected static string $resource = CalendarEventResource::class;

    public function form(Form $form): Form
    {
        /** @var User $user */
        $user = auth()->user();

        return $form->schema([
            TextInput::make('title')
                ->string()
                ->required(),
            Textarea::make('description')
                ->string()
                ->nullable(),
            DateTimePicker::make('starts_at')
                ->timezone($user->timezone)
                ->required(),
            DateTimePicker::make('ends_at')
                ->timezone($user->timezone)
                ->required(),
            TagsInput::make('emails')
                ->label('Attendees')
                ->placeholder('Add attendee email')
                ->nestedRecursiveRules(['email']),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
