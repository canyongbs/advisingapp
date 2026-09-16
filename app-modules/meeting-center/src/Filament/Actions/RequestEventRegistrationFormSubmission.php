<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Filament\Actions;

use AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

class RequestEventRegistrationFormSubmission extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->steps([
            Step::make('Event')
                ->schema([
                    Select::make('event_id')
                        ->label('Event')
                        ->required()
                        ->searchable()
                        ->options(fn (): array => Event::query()
                            ->whereHas('eventRegistrationForm')
                            ->limit(50)
                            ->pluck('title', 'id')
                            ->all())
                        ->getSearchResultsUsing(fn (string $search): array => Event::query()
                            ->whereHas('eventRegistrationForm')
                            ->where(new Expression('lower(title)'), 'like', '%' . Str::lower($search) . '%')
                            ->limit(50)
                            ->pluck('title', 'id')
                            ->all())
                        ->getOptionLabelUsing(fn (string | int | null $value): ?string => filled($value)
                            ? Event::query()
                                ->whereKey($value)
                                ->value('title')
                            : null),
                ]),
            Step::make('Notification')
                ->schema([
                    Textarea::make('request_note')
                        ->label('Note')
                        ->columnSpanFull(),
                ]),
        ]);

        $this->action(function (array $data, Action $action, ManageRelatedRecords | RelationManager $livewire) {
            $owner = $livewire->getOwnerRecord();
            assert($owner instanceof Educatable);

            $event = Event::query()->whereKey($data['event_id'])->whereHas('eventRegistrationForm')->first();

            if (! $event) {
                Notification::make()
                    ->title('This event no longer accepts registration requests')
                    ->danger()
                    ->send();

                $action->halt();

                return;
            }

            if (blank($owner->primaryEmailAddress?->address)) {
                Notification::make()
                    ->title('This record does not have a primary email address to send the request to')
                    ->danger()
                    ->send();

                $action->halt();

                return;
            }

            $form = $event->eventRegistrationForm;

            $attendee = $event->attendees()->firstOrNew(['email' => $owner->primaryEmailAddress->address]);

            if ($attendee->exists && $attendee->isArchived()) {
                $attendee->unarchive();
            }

            if (blank($attendee->status)) {
                $attendee->status = EventAttendeeStatus::Invited;
            }

            $attendee->save();

            $submission = $attendee->submissions()->requested()->firstOrNew(['form_id' => $form->id]);

            if (blank($submission->attendee_status)) {
                $submission->attendee_status = EventAttendeeStatus::Invited;
            }

            $submission->request_method = FormSubmissionRequestDeliveryMethod::Email;
            $submission->request_note = $data['request_note'] ?? null;
            $submission->requester()->associate(auth()->user());
            $submission->save();

            $submission->deliverRequest();

            Notification::make()
                ->title('Event registration request sent')
                ->success()
                ->send();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'request';
    }
}
