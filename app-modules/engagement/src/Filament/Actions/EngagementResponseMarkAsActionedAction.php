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

use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\HolisticEngagement;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Timeline\Models\Timeline;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EngagementResponseMarkAsActionedAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Mark as Actioned')
            ->modalHeading(fn (?Model $record): string => 'Mark ' . $this->resolveHeadingTypeLabel($record) . ' as Actioned')
            ->modalDescription(function (?Model $record): string {
                return 'When you action ' . $this->resolveTypeLabel($record) . ', you are indicating that you have taken all necessary steps to respond to this ' . $this->resolveSenderLabel($record) . '. Please describe below what steps you have taken.';
            })
            ->schema([
                Textarea::make('note')
                    ->label('Note')
                    ->required(),
            ])
            ->modalSubmitActionLabel('Mark as Actioned')
            ->modalFooterActions(fn (): array => array_filter([
                $this->getModalCancelAction(),
                $this->getModalSubmitAction(),
            ]))
            ->action(function (array $data, ?Model $record) {
                $engagementResponse = $this->getEngagementResponse($record);

                if ($engagementResponse === null) {
                    Notification::make()
                        ->title('Something went wrong!')
                        ->danger()
                        ->send();

                    return;
                }

                DB::transaction(function () use ($data, $engagementResponse): void {
                    $engagementResponse->actionedNotes()->create([
                        'note' => $data['note'],
                    ]);
                    $engagementResponse->update(['status' => EngagementResponseStatus::Actioned]);
                });

                Notification::make()
                    ->title($this->resolveHeadingTypeLabel($record) . ' marked as actioned')
                    ->success()
                    ->send();
            });
    }

    public function getTypeLabel(?EngagementResponse $engagementResponse): ?string
    {
        return match ($engagementResponse?->type) {
            EngagementResponseType::Email => 'an email',
            EngagementResponseType::Sms => 'a text',
            default => null,
        };
    }

    public function resolveTypeLabel(?Model $record): ?string
    {
        return $this->getTypeLabel($this->getEngagementResponse($record));
    }

    public function getSenderLabel(?EngagementResponse $engagementResponse): ?string
    {
        return match (true) {
            $engagementResponse?->sender instanceof Student => 'student',
            $engagementResponse?->sender instanceof Prospect => 'prospect',
            default => null,
        };
    }

    public function resolveSenderLabel(?Model $record): ?string
    {
        return $this->getSenderLabel($this->getEngagementResponse($record));
    }

    public function getEngagementResponse(?Model $record): ?EngagementResponse
    {
        return match (true) {
            $record instanceof EngagementResponse => $record,
            $record instanceof HolisticEngagement && $record->record instanceof EngagementResponse => $record->record,
            $record instanceof Timeline && $record->timelineable instanceof EngagementResponse => $record->timelineable,
            default => null,
        };
    }

    public function getHeadingTypeLabel(?EngagementResponse $engagementResponse): ?string
    {
        return match ($engagementResponse?->type) {
            EngagementResponseType::Email => 'Email',
            EngagementResponseType::Sms => 'Text',
            default => null,
        };
    }

    public function resolveHeadingTypeLabel(?Model $record): ?string
    {
        return $this->getHeadingTypeLabel($this->getEngagementResponse($record));
    }
}
