<?php

namespace AdvisingApp\MeetingCenter\Filament\Actions\Table;

use App\Models\User;
use Livewire\Component;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Jobs\CreateEventAttendees;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;

class InviteAttendeeAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Invite')
            ->icon('heroicon-o-envelope')
            ->form([
                Select::make('event')
                    ->options(function (Component $livewire, ?Educatable $record) {
                        $record ??= $livewire->getRecord();

                        return Event::whereNotIn('id', $record->eventAttendeeRecords()->pluck('event_id'))
                            ->pluck('title', 'id');
                    })
                    ->searchable()
                    ->required(),
            ])
            ->action(function (array $data, Component $livewire, ?Educatable $record) {
                /** @var User $user */
                $user = auth()->user();

                $record ??= $livewire->getRecord();

                dispatch(new CreateEventAttendees(Event::find($data['event']), [$record->email], $user));

                Notification::make()
                    ->title('The invitation is being sent')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'invite';
    }
}
