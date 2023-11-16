<?php

namespace Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DateTimePicker;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;

class CreateCalendarEvent extends CreateRecord
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
            TagsInput::make('attendees')
                ->placeholder('Add attendee email')
                ->default([$user->calendar->provider_email])
                ->nestedRecursiveRules(['email']),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var User $user */
        $user = auth()->user();

        $data = parent::mutateFormDataBeforeCreate($data);
        $data['calendar_id'] = $user->calendar->id;

        return $data;
    }
}
