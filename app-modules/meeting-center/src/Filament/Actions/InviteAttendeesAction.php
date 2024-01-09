<?php

namespace AdvisingApp\MeetingCenter\Filament\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\TagsInput;
use Filament\Notifications\Notification;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Jobs\CreateEventAttendees;

class InviteAttendeesAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Invite')
            ->icon('heroicon-o-envelope')
            ->form([
                TagsInput::make('attendees')
                    ->placeholder('Add attendee email')
                    ->nestedRecursiveRules(['email'])
                    ->required(),
            ])
            ->action(function (array $data, Event $record) {
                /** @var User $user */
                $user = auth()->user();

                $emails = $data['attendees'];

                dispatch(new CreateEventAttendees($record, $emails, $user));

                Notification::make()
                    ->title(count($emails) > 1 ? 'The invitations are being sent' : 'The invitation is being sent')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'invite';
    }
}
