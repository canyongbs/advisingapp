<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use App\Models\User;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\MeetingCenter\Models\Event;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Jobs\CreateEventAttendees;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\MeetingCenter\Filament\Resources\EventResource;

class ManageProspectEvents extends ManageRelatedRecords
{
    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'eventAttendeeRecords';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Events';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Events';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('event.title')
                    ->url(fn (EventAttendee $record) => EventResource::getUrl('view', ['record' => $record->event]))
                    ->color('primary'),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->headerActions([
                Action::make('Invite')
                    ->icon('heroicon-o-envelope')
                    ->form([
                        Select::make('event')
                            ->options(function () {
                                /** @var Educatable $record */
                                $record = $this->getRecord();

                                return Event::whereNotIn('id', $record->eventAttendeeRecords()->pluck('event_id'))
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        /** @var User $user */
                        $user = auth()->user();

                        /** @var Educatable $record */
                        $record = $this->getRecord();

                        dispatch(new CreateEventAttendees(Event::find($data['event']), [$record->email], $user));
                    }),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                EventAttendeeStatus::Invited,
                EventAttendeeStatus::Attending,
            ]));
    }
}
