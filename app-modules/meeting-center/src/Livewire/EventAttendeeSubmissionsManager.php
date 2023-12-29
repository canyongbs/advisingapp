<?php

namespace AdvisingApp\MeetingCenter\Livewire;

use Livewire\Component;
use Filament\Tables\Table;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;

class EventAttendeeSubmissionsManager extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public EventAttendee $record;

    public function render()
    {
        return view('meeting-center::livewire.event-attendee-submissions-manager');
    }

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn (): HasMany => $this->record->submissions())
            ->inverseRelationship('author')
            ->columns([
                TextColumn::make('attendee_status')
                    ->badge(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (EventRegistrationFormSubmission $record) => 'Submission Details: ' . $record->submitted_at->format('M j, Y H:i:s'))
                    ->infolist(fn (EventRegistrationFormSubmission $record): array => [
                        Section::make('Authenticated author')
                            ->schema([
                                TextEntry::make('author.email')
                                    ->label('Email Address'),
                            ])
                            ->columns(),
                    ]),
                //->modalContent(fn (FormSubmission $record) => view('form::submission', ['submission' => $record])),
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
