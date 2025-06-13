<?php

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
            ->whereNotNull('sender')
            ->get()
            ->each(function (UnmatchedInboundCommunication $communication) {
                if ($communication->type === EngagementResponseType::Email) {
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

                    return;
                } elseif ($communication->type === EngagementResponseType::Sms) {
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
                    }

                    $communication->delete();
                }
            });
    }
}
