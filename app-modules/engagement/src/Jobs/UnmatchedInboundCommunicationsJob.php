<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Engagement\Jobs;

use AdvisingApp\Engagement\Actions\Contracts\EngagementResponseSenderFinder;
use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\UnmatchedInboundCommunication;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UnmatchedInboundCommunicationsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        UnmatchedInboundCommunication::query()
            ->chunkById(100, function ($communications) {
                foreach ($communications as $communication) {
                    match ($communication->type) {
                        EngagementResponseType::Email => $this->processEmail($communication),
                        EngagementResponseType::Sms => $this->processSms($communication),
                    };
                }
            });
    }

    protected function processEmail(UnmatchedInboundCommunication $communication): void
    {
        $students = Student::query()
            ->whereRelation('emailAddresses', 'address', $communication->sender)
            ->get();

        if ($students->isNotEmpty()) {
            $students->each(function (Student $student) use ($communication) {
                $student->engagementResponses()
                    ->create([
                        'subject' => $communication->subject,
                        'content' => $communication->body,
                        'sent_at' => $communication->occurred_at,
                        'type' => EngagementResponseType::Email,
                        'status' => EngagementResponseStatus::New,
                    ]);
            });
            $communication->delete();

            return;
        }

        $prospects = Prospect::query()
            ->whereRelation('emailAddresses', 'address', $communication->sender)
            ->get();

        if ($prospects->isEmpty()) {
            return;
        }

        $prospects->each(function (Prospect $prospect) use ($communication) {
            $prospect->engagementResponses()
                ->create([
                    'subject' => $communication->subject,
                    'content' => $communication->body,
                    'sent_at' => $communication->occurred_at,
                    'type' => EngagementResponseType::Email,
                    'status' => EngagementResponseStatus::New,
                ]);
        });

        $communication->delete();
    }

    protected function processSms(UnmatchedInboundCommunication $communication): void
    {
        $finder = app(EngagementResponseSenderFinder::class);

        $sender = $finder->find($communication->sender);

        if (! is_null($sender)) {
            EngagementResponse::create([
                'type' => EngagementResponseType::Sms,
                'sender_id' => $sender->getKey(),
                'sender_type' => $sender->getMorphClass(),
                'content' => $communication->body,
                'sent_at' => $communication->occurred_at,
                'status' => EngagementResponseStatus::New,
            ]);

            $communication->delete();
        }
    }
}
