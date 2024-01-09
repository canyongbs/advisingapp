<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\MeetingCenter\Filament\Resources\EventResource;
use AdvisingApp\MeetingCenter\Filament\Actions\Table\InviteAttendeeAction;

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
                InviteAttendeeAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                EventAttendeeStatus::Invited,
                EventAttendeeStatus::Attending,
            ]));
    }
}
