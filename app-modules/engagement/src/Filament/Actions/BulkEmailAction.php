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

namespace AdvisingApp\Engagement\Filament\Actions;

use AdvisingApp\Engagement\Actions\CreateEngagementBatch;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Engagement\Filament\Schemas\Components\EngagementEmailBodyInput;
use AdvisingApp\Engagement\Filament\Schemas\Components\EngagementScheduledAtDateTimePicker;
use AdvisingApp\Engagement\Filament\Schemas\Components\EngagementSendLaterToggle;
use AdvisingApp\Engagement\Filament\Schemas\Components\EngagementSubjectInput;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use Filament\Actions\BulkAction;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BulkEmailAction
{
    public static function make(string $context): BulkAction
    {
        return BulkAction::make('send_email')
            ->label('Send Email')
            ->icon('heroicon-o-envelope')
            ->slideOver()
            ->modalHeading('Send Bulk Email')
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} {$context} to send email.")
            ->model(EngagementBatch::class)
            ->steps([
                Step::make('Engagement Details')
                    ->description("Add the details that will be sent to the selected {$context}")
                    ->schema([
                        EngagementSubjectInput::make(),
                        EngagementEmailBodyInput::make(),
                        Actions::make([
                            BulkDraftWithAiAction::make()
                                ->mergeTags(Engagement::getMergeTags()),
                        ]),
                    ]),
                Step::make('Schedule')
                    ->description('Choose when you would like to send this engagement.')
                    ->schema([
                        EngagementSendLaterToggle::make(),
                        EngagementScheduledAtDateTimePicker::make(),
                    ]),
            ])
            ->action(function (Collection $records, array $data, Schema $schema) {
                /** @var Collection<int, CanBeNotified> $records */
                app(CreateEngagementBatch::class)->execute(new EngagementCreationData(
                    user: Auth::user(),
                    recipient: $records->filter(fn (CanBeNotified $record) => $record->canReceiveEmail()),
                    channel: NotificationChannel::Email,
                    subject: $data['subject'] ?? null,
                    body: $data['body'] ?? null,
                    scheduledAt: ($data['send_later'] ?? false) ? Carbon::parse($data['scheduled_at'] ?? null) : null,
                    schema: $schema,
                ));
            })
            ->modalSubmitActionLabel('Send')
            ->deselectRecordsAfterCompletion()
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false);
    }
}
