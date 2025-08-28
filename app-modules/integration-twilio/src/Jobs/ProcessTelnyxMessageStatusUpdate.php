<?php

namespace AdvisingApp\IntegrationTwilio\Jobs;

use AdvisingApp\IntegrationAwsSesEventHandling\Exceptions\CouldNotFindSmsMessageFromData;
use AdvisingApp\Notification\Enums\SmsMessageEventType;
use AdvisingApp\Notification\Models\SmsMessage;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessTelnyxMessageStatusUpdate implements ShouldQueue
{
    use Queueable;

    /**
     * @param array{
     *     payload: array{
     *         id: string,
     *         to: array<array{status: string}>
     *     },
     *     occurred_at: string
     * } $data
     */
    public function __construct(
        protected array $data,
    ) {}

    public function handle(): void
    {
        $smsMessage = SmsMessage::query()
            ->where('external_reference_id', $this->data['payload']['id'])
            ->first();

        if (is_null($smsMessage)) {
            report(new CouldNotFindSmsMessageFromData($this->data));

            return;
        }

        $smsMessage->events()->create([
            'type' => match ($this->data['payload']['to'][0]['status']) {
                'queued', 'sending' => SmsMessageEventType::Queued,
                'sent' => SmsMessageEventType::Sent,
                'delivered' => SmsMessageEventType::Delivered,
                'sending_failed', 'delivery_failed', 'delivery_unconfirmed' => SmsMessageEventType::Failed,
                default => throw new Exception('Unknown SMS message status'),
            },
            'payload' => $this->data,
            'occurred_at' => $this->data['occurred_at'],
        ]);
    }
}
