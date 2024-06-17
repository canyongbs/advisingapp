<?php

namespace AdvisingApp\IntegrationTwilio\Jobs;

use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Twilio\Exceptions\TwilioException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\Notification\Models\OutboundDeliverable;

class CheckSmsOutboundDeliverableStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public OutboundDeliverable $deliverable,
    ) {}

    public function handle(Client $twilioClient): void
    {
        try {
            $messageInstance = $twilioClient->messages($this->deliverable->external_reference_id)->fetch();

            $this->deliverable->update([
                'external_status' => $messageInstance->status,
            ]);

            // TODO Update related entity if necessary

            match ($messageInstance->status) {
                'delivered' => $this->deliverable->markDeliverySuccessful(Carbon::parse($messageInstance->dateSent)),
                'undelivered', 'failed' => $this->deliverable->markDeliveryFailed($messageInstance->errorMessage ?? 'Message could not successfully be delivered.'),
                default => null,
            };
        } catch (TwilioException $e) {
            report($e);
        }
    }
}
