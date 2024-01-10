<?php

namespace AdvisingApp\MeetingCenter\Filament\Actions\Table;

use Filament\Tables\Actions\ViewAction;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Infolists\Components\RepeatableEntry;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;

class ViewEventAttendeeAction extends ViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn (EventAttendee $record) => $record->event->title . ' - ' . $record->email)
            ->infolist(fn (EventAttendee $record): array => [
                Fieldset::make('Attendee Info')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                        TextEntry::make('email')
                            ->label('Email address'),
                    ])
                    ->columns(),

                Fieldset::make('Relations')
                    ->schema([
                        RepeatableEntry::make('prospects')
                            ->schema([
                                TextEntry::make(Prospect::displayNameKey())
                                    ->label('Name')
                                    ->color('primary')
                                    ->url(fn (Prospect $record): string => ProspectResource::getUrl('view', ['record' => $record])),
                            ])
                            ->columns()
                            ->visible(fn (EventAttendee $record): bool => $record->prospects->isNotEmpty()),
                        RepeatableEntry::make('students')
                            ->schema([
                                TextEntry::make(Student::displayNameKey())
                                    ->label('Name')
                                    ->color('primary')
                                    ->url(fn (Student $record): string => StudentResource::getUrl('view', ['record' => $record])),
                            ])
                            ->columns()
                            ->visible(fn (EventAttendee $record): bool => $record->students->isNotEmpty()),
                    ])
                    ->visible(fn (EventAttendee $record): bool => $record->prospects->isNotEmpty() || $record->students->isNotEmpty())
                    ->columns(),

                Fieldset::make('Attendee Submissions')
                    ->schema([
                        // TODO: Look into Livewire/Filament bug that prevents us from passing the class here, requiring that we define it in the Service Provider
                        // TODO: Look into bug where, without lazy load, you have to click view twice to get the modal to show
                        Livewire::make('event-attendee-submissions-manager')->lazy()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getDefaultName(): ?string
    {
        return 'viewEventAttendee';
    }
}
